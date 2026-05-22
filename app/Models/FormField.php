<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_template_id',
        'type',
        'key',
        'label_ar',
        'label_en',
        'placeholder_ar',
        'placeholder_en',
        'content_ar',
        'content_en',
        'options',
        'settings',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'settings' => 'array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }
}
