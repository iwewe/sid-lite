<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama',
        'dusun',
        'rw',
        'rt',
        'alamat',
        'no_kk',
        'tanggal_lahir',
        'jenis_kelamin',
        'telepon',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get all module responses for this warga
     */
    public function moduleResponses()
    {
        return $this->hasMany(ModuleResponse::class);
    }

    /**
     * Get module response for specific module
     */
    public function getResponseForModule($moduleCode)
    {
        return $this->moduleResponses()
            ->whereHas('module', function($query) use ($moduleCode) {
                $query->where('code', $moduleCode);
            })
            ->first();
    }

    /**
     * Scope: Search by NIK or Name
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope: Filter by wilayah
     */
    public function scopeFilterWilayah($query, $dusun = null, $rw = null, $rt = null)
    {
        if (!empty($dusun)) {
            $query->where('dusun', 'LIKE', "%{$dusun}%");
        }
        if (!empty($rw)) {
            $query->where('rw', $rw);
        }
        if (!empty($rt)) {
            $query->where('rt', $rt);
        }
        return $query;
    }

    /**
     * Get verification status summary
     */
    public function getVerificationSummaryAttribute()
    {
        $responses = $this->moduleResponses()
            ->with('module')
            ->get();

        return $responses->map(function($response) {
            return [
                'module_code' => $response->module->code,
                'module_name' => $response->module->name,
                'is_verified' => $response->is_verified,
                'score' => $response->verification_score,
                'min_verified' => $response->module->min_verified,
            ];
        });
    }
}
