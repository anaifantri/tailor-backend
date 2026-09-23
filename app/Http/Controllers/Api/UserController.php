<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $fields = ['ulid', 'is_active', 'name', 'username', 'email', 'phone', 'email_verified_at'];
        
        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search', null);

        $users = $this->userService->getAll($perPage, $search, $fields);
        
        return response()->json($users, 200);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $fields = ['ulid', 'is_active', 'name', 'username', 'email', 'phone', 'photo', 'email_verified_at'];

            $user = $this->userService->getByUlid($ulid, $fields);

            return response()->json([
                'status' => 'success',
                'data'   => new UserResource($user)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = $this->userService->create($validatedData);

        event(new Registered($user));
        
        return response()->json([
            'message' => 'Penambahan pengguna baru berhasil..!!',
            'data'    => new UserResource($user),
        ], 201);
    }

    public function update(UserRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            if (empty($validatedData['password'])) {
                unset($validatedData['password']);
            }

            $user = $this->userService->update($ulid, $validatedData);
            
            return response()->json([
                'message' => 'Data pengguna berhasil diperbarui.',
                'data'    => new UserResource($user)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $user = $this->userService->findByUlid($ulid);
            $this->userService->delete($ulid);

            return response()->json([
                'message' => 'Berhasil menghapus data user dengan nama ' . $user->name,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link verifikasi baru telah dikirim ke email Anda.']);
    }

	public function verify(Request $request): JsonResponse
	{
		$ulid = $request->route('ulid');
		$hash = $request->route('hash');

		if (! $request->hasValidSignature()) {
			return response()->json(['message' => 'Tautan verifikasi tidak valid atau sudah kedaluwarsa.'], 403);
		}

		try {
			$user = $this->userService->findByUlid($ulid);

			if (! $user) {
				return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
			}

			if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
				return response()->json(['message' => 'Tautan verifikasi tidak valid.'], 403);
			}

			if ($user->hasVerifiedEmail()) {
				return response()->json(['message' => 'Email sudah terverifikasi sebelumnya.'], 200);
			}

			if ($user->markEmailAsVerified()) {
				event(new Verified($user));
			}

			return response()->json(['message' => 'Email berhasil diverifikasi!'], 200);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
		}
	}

    public function changePassword(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->userService->changePassword($request->user(), $validatedData);

        return response()->json([
            'message' => 'Password berhasil diperbarui.'
        ], 200);
    }
}