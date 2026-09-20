<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'is_active', 'name', 'username', 'email', 'phone', 'email_verified_at'];
		
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);

        $users = $this->userService->getAll($perPage, $search, $fields);
		
		return response()->json($users, 200);

        // return response()->json(UserResource::collection($users));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'is_active', 'name', 'username', 'email', 'phone', 'photo', 'email_verified_at'];

            $user = $this->userService->getByHashedId($hashedId, $fields);

            return response()->json(new UserResource(['user' => $user]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link verifikasi baru telah dikirim ke email Anda.']);
    }

     public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Tautan verifikasi tidak valid.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi sebelumnya.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email berhasil diverifikasi!']);
    }
    
    public function changePassword(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->userService->changePassword($request->user(), $validatedData);

        return response()->json([
            'message' => 'Password berhasil diperbarui. Anda telah keluar secara otomatis.'
        ], 200);
    }

    public function store(UserRequest $request)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $validateData = $request->validated();

        $validateData['password'] = $request->password;
        $validateData['password'] = Hash::make($validateData['password']);

        $user = $this->userService->create($validateData);

        event(new Registered($user));
        
        $token = $user->createToken('riori')->plainTextToken;
        return response()->json(new UserResource([
            'message' => 'Penambahan pengguna baru berhasil..!!',
            'user' => $user, 
            //'token' => $token
            ]), 201);
    }

    public function update(UserRequest $request, string $hashedId)
    {
        try {
            if($request->password){
                $request->validate([
                    'password' => 'required|min:6',
                ]);
            }

            $validateData = $request->validated();

            if($request->password){
                $validateData['password'] = $request->password;
                $validateData['password'] = Hash::make($validateData['password']);
            }
            
            $user = $this->userService->update($request->id, $validateData);
            
            return response()->json(new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
			$user = $this->userService->findByHashedId($hashedId);
            $this->userService->delete($hashedId);
            return response()->json([
                'message' => 'Berhasil menghapus data user dengan nama ' . $user->name,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
}
