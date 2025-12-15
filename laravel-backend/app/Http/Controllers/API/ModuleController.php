<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Get all active modules
     */
    public function index(): JsonResponse
    {
        $modules = Module::active()->with('questions')->get();

        return response()->json([
            'success' => true,
            'data' => $modules->map(function($module) {
                return [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                    'description' => $module->description,
                    'min_verified' => $module->min_verified,
                    'icon' => $module->icon,
                    'questions_count' => $module->questions->count(),
                    'statistics' => $module->statistics,
                ];
            }),
        ]);
    }

    /**
     * Get single module with all questions
     */
    public function show($code): JsonResponse
    {
        $module = Module::where('code', $code)
            ->with(['questions' => function($query) {
                $query->orderBy('order');
            }])
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $module->id,
                'code' => $module->code,
                'name' => $module->name,
                'description' => $module->description,
                'min_verified' => $module->min_verified,
                'icon' => $module->icon,
                'questions' => $module->questions->map(function($q) {
                    return [
                        'id' => $q->id,
                        'code' => $q->code,
                        'question' => $q->question,
                        'field_type' => $q->field_type,
                        'options' => $q->formatted_options,
                        'is_required' => $q->is_required,
                        'help_text' => $q->help_text,
                    ];
                }),
                'statistics' => $module->statistics,
            ],
        ]);
    }

    /**
     * Create new module (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:modules,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_verified' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.code' => 'required|string|max:50',
            'questions.*.question' => 'required|string',
            'questions.*.field_type' => 'required|in:select,text,number,date,textarea',
            'questions.*.options' => 'nullable|array',
            'questions.*.is_required' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $module = Module::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'min_verified' => $request->min_verified,
                'icon' => $request->icon,
                'order' => Module::max('order') + 1,
            ]);

            foreach ($request->questions as $index => $questionData) {
                ModuleQuestion::create([
                    'module_id' => $module->id,
                    'code' => $questionData['code'],
                    'question' => $questionData['question'],
                    'field_type' => $questionData['field_type'],
                    'options' => $questionData['options'] ?? null,
                    'is_required' => $questionData['is_required'] ?? false,
                    'order' => $index,
                    'help_text' => $questionData['help_text'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Module berhasil dibuat',
                'data' => $module->load('questions'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat module: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update module
     */
    public function update(Request $request, $code): JsonResponse
    {
        $module = Module::where('code', $code)->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_verified' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $module->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Module berhasil diupdate',
            'data' => $module,
        ]);
    }

    /**
     * Delete module (soft delete - set inactive)
     */
    public function destroy($code): JsonResponse
    {
        $module = Module::where('code', $code)->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        $module->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Module berhasil dinonaktifkan',
        ]);
    }
}
