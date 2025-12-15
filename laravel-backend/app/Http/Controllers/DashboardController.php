<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Module;
use App\Models\ModuleResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        // Overview statistics
        $stats = [
            'total_warga' => Warga::count(),
            'total_modules' => Module::active()->count(),
            'total_responses' => ModuleResponse::count(),
            'verified_responses' => ModuleResponse::where('is_verified', true)->count(),
            'total_users' => User::active()->count(),
        ];

        $stats['verification_rate'] = $stats['total_responses'] > 0
            ? round(($stats['verified_responses'] / $stats['total_responses']) * 100, 2)
            : 0;

        // Module statistics
        $moduleStats = Module::active()->get()->map(function($module) {
            $total = $module->responses()->count();
            $verified = $module->responses()->where('is_verified', true)->count();

            return [
                'code' => $module->code,
                'name' => $module->name,
                'icon' => $module->icon,
                'total_responses' => $total,
                'verified' => $verified,
                'pending' => $total - $verified,
                'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            ];
        });

        // Recent responses
        $recentResponses = ModuleResponse::with(['warga', 'module', 'submittedBy'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($response) {
                return [
                    'id' => $response->id,
                    'warga_nama' => $response->warga->nama,
                    'warga_nik' => $response->warga->nik,
                    'module_name' => $response->module->name,
                    'is_verified' => $response->is_verified,
                    'score' => $response->verification_score,
                    'min_verified' => $response->module->min_verified,
                    'submitted_at' => $response->submitted_at?->format('d/m/Y H:i'),
                    'submitted_by' => $response->submittedBy?->name ?? '-',
                ];
            });

        // Verification statistics by wilayah
        $wilayahStats = DB::table('warga')
            ->select('dusun', DB::raw('count(*) as total'))
            ->groupBy('dusun')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'moduleStats',
            'recentResponses',
            'wilayahStats'
        ));
    }

    /**
     * Show form page
     */
    public function form()
    {
        return view('dashboard.form');
    }
}
