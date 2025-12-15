<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WargaController extends Controller
{
    /**
     * Search warga by NIK, nama, or wilayah
     */
    public function search(Request $request): JsonResponse
    {
        $query = Warga::query();

        // Search by NIK or Nama
        if ($request->has('q') && !empty($request->q)) {
            $query->search($request->q);
        }

        // Filter by wilayah
        $query->filterWilayah(
            $request->dusun,
            $request->rw,
            $request->rt
        );

        $warga = $query->orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil diambil',
            'data' => $warga->map(function($w) {
                return [
                    'nik' => $w->nik,
                    'nama' => $w->nama,
                    'dusun' => $w->dusun,
                    'rw' => $w->rw,
                    'rt' => $w->rt,
                    'alamat' => $w->alamat,
                ];
            }),
            'count' => $warga->count(),
        ]);
    }

    /**
     * Get single warga by NIK
     */
    public function show($nik): JsonResponse
    {
        $warga = Warga::where('nik', $nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Warga tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nik' => $warga->nik,
                'nama' => $warga->nama,
                'dusun' => $warga->dusun,
                'rw' => $warga->rw,
                'rt' => $warga->rt,
                'alamat' => $warga->alamat,
                'no_kk' => $warga->no_kk,
                'tanggal_lahir' => $warga->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $warga->jenis_kelamin,
                'telepon' => $warga->telepon,
                'verification_summary' => $warga->verification_summary,
            ],
        ]);
    }

    /**
     * Create new warga
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama' => 'required|string|max:255',
            'dusun' => 'nullable|string|max:255',
            'rw' => 'nullable|string|max:10',
            'rt' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|max:16',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'telepon' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $warga = Warga::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil ditambahkan',
            'data' => $warga,
        ], 201);
    }

    /**
     * Update warga data
     */
    public function update(Request $request, $nik): JsonResponse
    {
        $warga = Warga::where('nik', $nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Warga tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'dusun' => 'nullable|string|max:255',
            'rw' => 'nullable|string|max:10',
            'rt' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|max:16',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'telepon' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $warga->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil diupdate',
            'data' => $warga,
        ]);
    }

    /**
     * Delete warga (soft delete)
     */
    public function destroy($nik): JsonResponse
    {
        $warga = Warga::where('nik', $nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Warga tidak ditemukan',
            ], 404);
        }

        $warga->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil dihapus',
        ]);
    }
}
