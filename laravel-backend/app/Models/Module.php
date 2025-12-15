<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'min_verified',
        'is_active',
        'order',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_verified' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get all questions for this module
     */
    public function questions()
    {
        return $this->hasMany(ModuleQuestion::class)->orderBy('order');
    }

    /**
     * Get all responses for this module
     */
    public function responses()
    {
        return $this->hasMany(ModuleResponse::class);
    }

    /**
     * Scope: Only active modules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Get module statistics
     */
    public function getStatisticsAttribute()
    {
        $total = $this->responses()->count();
        $verified = $this->responses()->where('is_verified', true)->count();
        $pending = $total - $verified;

        return [
            'total_responses' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
        ];
    }
}
