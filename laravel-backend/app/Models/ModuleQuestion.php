<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'code',
        'question',
        'field_type',
        'options',
        'is_required',
        'order',
        'help_text',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the module that owns the question
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Validate if a value is acceptable for this question
     */
    public function validateValue($value)
    {
        if ($this->is_required && empty($value)) {
            return false;
        }

        // Type-specific validation
        switch ($this->field_type) {
            case 'number':
                return is_numeric($value);
            case 'date':
                return strtotime($value) !== false;
            case 'select':
                if (empty($this->options)) return true;
                $validValues = collect($this->options)->pluck('value')->toArray();
                return in_array($value, $validValues);
            default:
                return true;
        }
    }

    /**
     * Get formatted options for frontend
     */
    public function getFormattedOptionsAttribute()
    {
        if ($this->field_type !== 'select' || empty($this->options)) {
            return null;
        }

        return collect($this->options)->map(function($option) {
            return [
                'value' => $option['value'] ?? $option,
                'label' => $option['label'] ?? $option,
            ];
        })->toArray();
    }
}
