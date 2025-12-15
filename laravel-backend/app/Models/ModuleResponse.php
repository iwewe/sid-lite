<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_id',
        'module_id',
        'responses',
        'verification_score',
        'is_verified',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'responses' => 'array',
        'verification_score' => 'integer',
        'is_verified' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($response) {
            $response->calculateVerificationScore();
        });
    }

    /**
     * Get the warga that owns the response
     */
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    /**
     * Get the module that owns the response
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the user who submitted the response
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Calculate verification score based on required questions filled
     */
    public function calculateVerificationScore()
    {
        if (empty($this->responses)) {
            $this->verification_score = 0;
            $this->is_verified = false;
            return;
        }

        $requiredQuestions = $this->module->questions()
            ->where('is_required', true)
            ->get();

        $score = 0;
        foreach ($requiredQuestions as $question) {
            $value = $this->responses[$question->code] ?? null;
            if (!empty($value) && $value !== '') {
                $score++;
            }
        }

        $this->verification_score = $score;
        $this->is_verified = $score >= $this->module->min_verified;
    }

    /**
     * Get response value for specific question
     */
    public function getResponseValue($questionCode)
    {
        return $this->responses[$questionCode] ?? null;
    }

    /**
     * Set response value for specific question
     */
    public function setResponseValue($questionCode, $value)
    {
        $responses = $this->responses ?? [];
        $responses[$questionCode] = $value;
        $this->responses = $responses;
    }

    /**
     * Merge new responses with existing ones
     */
    public function mergeResponses(array $newResponses)
    {
        $existing = $this->responses ?? [];
        $this->responses = array_merge($existing, $newResponses);
    }

    /**
     * Mark as submitted
     */
    public function markAsSubmitted($userId = null)
    {
        $this->submitted_at = now();
        $this->submitted_by = $userId;
        $this->save();
    }

    /**
     * Get detailed verification status
     */
    public function getDetailedStatusAttribute()
    {
        $requiredQuestions = $this->module->questions()
            ->where('is_required', true)
            ->get();

        $filled = [];
        $missing = [];

        foreach ($requiredQuestions as $question) {
            $value = $this->responses[$question->code] ?? null;
            if (!empty($value) && $value !== '') {
                $filled[] = [
                    'code' => $question->code,
                    'question' => $question->question,
                    'value' => $value,
                ];
            } else {
                $missing[] = [
                    'code' => $question->code,
                    'question' => $question->question,
                ];
            }
        }

        return [
            'is_verified' => $this->is_verified,
            'score' => $this->verification_score,
            'min_required' => $this->module->min_verified,
            'filled_questions' => $filled,
            'missing_questions' => $missing,
        ];
    }
}
