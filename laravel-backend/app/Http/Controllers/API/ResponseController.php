<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Module;
use App\Models\ModuleResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    /**
     * Get all responses for a warga
     */
    public function getWargaResponses($nik): JsonResponse
    {
        $warga = Warga::where('nik', $nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Warga tidak ditemukan',
            ], 404);
        }

        $responses = $warga->moduleResponses()
            ->with(['module', 'submittedBy'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $responses->map(function($response) {
                return [
                    'id' => $response->id,
                    'module_code' => $response->module->code,
                    'module_name' => $response->module->name,
                    'responses' => $response->responses,
                    'verification_score' => $response->verification_score,
                    'is_verified' => $response->is_verified,
                    'min_verified' => $response->module->min_verified,
                    'submitted_at' => $response->submitted_at?->format('Y-m-d H:i:s'),
                    'submitted_by' => $response->submittedBy?->name,
                    'detailed_status' => $response->detailed_status,
                ];
            }),
        ]);
    }

    /**
     * Get single response for warga + module
     */
    public function getModuleResponse($nik, $moduleCode): JsonResponse
    {
        $warga = Warga::where('nik', $nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Warga tidak ditemukan',
            ], 404);
        }

        $module = Module::where('code', $moduleCode)->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        $response = ModuleResponse::where('warga_id', $warga->id)
            ->where('module_id', $module->id)
            ->with(['module', 'submittedBy'])
            ->first();

        if (!$response) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada data response',
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $response->id,
                'warga' => [
                    'nik' => $warga->nik,
                    'nama' => $warga->nama,
                ],
                'module' => [
                    'code' => $module->code,
                    'name' => $module->name,
                    'min_verified' => $module->min_verified,
                ],
                'responses' => $response->responses,
                'verification_score' => $response->verification_score,
                'is_verified' => $response->is_verified,
                'submitted_at' => $response->submitted_at?->format('Y-m-d H:i:s'),
                'submitted_by' => $response->submittedBy?->name,
                'detailed_status' => $response->detailed_status,
            ],
        ]);
    }

    /**
     * Save or update response (from frontend mockup "Simpan ke Database")
     */
    public function saveResponse(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|exists:warga,nik',
            'module_code' => 'required|string|exists:modules,code',
            'responses' => 'required|array',
            'submit' => 'boolean', // true = submit final, false = save draft
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $warga = Warga::where('nik', $request->nik)->first();
        $module = Module::where('code', $request->module_code)->first();

        // Find or create response
        $response = ModuleResponse::firstOrNew([
            'warga_id' => $warga->id,
            'module_id' => $module->id,
        ]);

        // Merge responses
        $response->mergeResponses($request->responses);

        // If submit = true, mark as submitted
        if ($request->submit === true) {
            $response->markAsSubmitted(auth()->id());
        }

        $response->save();

        // Recalculate score after save
        $response->calculateVerificationScore();
        $response->save();

        return response()->json([
            'success' => true,
            'message' => $request->submit
                ? 'Data berhasil disimpan ke database'
                : 'Draft berhasil disimpan',
            'data' => [
                'id' => $response->id,
                'verification_score' => $response->verification_score,
                'is_verified' => $response->is_verified,
                'min_verified' => $module->min_verified,
                'submitted_at' => $response->submitted_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Delete response
     */
    public function deleteResponse($id): JsonResponse
    {
        $response = ModuleResponse::find($id);

        if (!$response) {
            return response()->json([
                'success' => false,
                'message' => 'Response tidak ditemukan',
            ], 404);
        }

        $response->delete();

        return response()->json([
            'success' => true,
            'message' => 'Response berhasil dihapus',
        ]);
    }

    /**
     * Dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        $totalWarga = Warga::count();
        $totalModules = Module::active()->count();
        $totalResponses = ModuleResponse::count();
        $verifiedResponses = ModuleResponse::where('is_verified', true)->count();

        $moduleStats = Module::active()->get()->map(function($module) {
            return [
                'module_code' => $module->code,
                'module_name' => $module->name,
                'statistics' => $module->statistics,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_warga' => $totalWarga,
                    'total_modules' => $totalModules,
                    'total_responses' => $totalResponses,
                    'verified_responses' => $verifiedResponses,
                    'verification_rate' => $totalResponses > 0
                        ? round(($verifiedResponses / $totalResponses) * 100, 2)
                        : 0,
                ],
                'modules' => $moduleStats,
            ],
        ]);
    }
}
