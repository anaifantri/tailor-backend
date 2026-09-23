<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TailorAssignmentRequest;
use App\Services\TailorAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TailorAssignmentController extends Controller
{
    public function __construct(
        protected TailorAssignmentService $tailorAssignmentService
    ) {}

    /**
     * Menampilkan daftar penugasan penjahit.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));
        $search = $request->input('search');

        $assignments = $this->tailorAssignmentService->getAllAssignments(
            (int) $perPage,
            $month ? (int) $month : null,
            (int) $year,
            $search
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data penugasan penjahit',
            'data'    => $assignments,
        ]);
    }

    /**
     * Menyimpan data penugasan penjahit baru.
     */
    public function store(TailorAssignmentRequest $request): JsonResponse
    {
        $assignment = $this->tailorAssignmentService->createAssignment($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Penugasan penjahit berhasil ditambahkan',
            'data'    => $assignment,
        ], 201);
    }

    /**
     * Menampilkan detail penugasan penjahit berdasarkan ID terdekripsi.
     */
    public function show(string $hashedId): JsonResponse
    {
        // Parameter 'id' sudah hasil dekripsi dari prepareForValidation()
        // $id = $request->input('id'); 
        $assignment = $this->tailorAssignmentService->getAssignmentById($hashedId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail penugasan penjahit berhasil ditemukan',
            'data'    => $assignment,
        ]);
    }

    /**
     * Memperbarui data penugasan penjahit.
     */
    public function update(TailorAssignmentRequest $request): JsonResponse
    {
        // Parameter 'id' sudah hasil dekripsi dari prepareForValidation()
        $id = $request->input('id');
        $assignment = $this->tailorAssignmentService->updateAssignment($id, $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Penugasan penjahit berhasil diperbarui',
            'data'    => $assignment,
        ]);
    }

    /**
     * Memperbarui status penugasan penjahit.
     */
    public function updateStatus(TailorAssignmentRequest $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:assigned,in_progress,completed,cancelled'],
        ], [
            'status.required' => 'Status wajib diisi.',
            'status.in'       => 'Pilihan status tidak valid.',
        ]);

        $id = $request->input('id');
        $assignment = $this->tailorAssignmentService->updateAssignmentStatus($id, $request->input('status'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Status penugasan penjahit berhasil diperbarui',
            'data'    => $assignment,
        ]);
    }

    /**
     * Menghapus data penugasan penjahit.
     */
    public function destroy(string $hashedId): JsonResponse
    {
        $this->tailorAssignmentService->deleteAssignment($hashedId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Penugasan penjahit berhasil dihapus',
        ]);
    }
}