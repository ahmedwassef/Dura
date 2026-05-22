<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class FormTemplate extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'category_code',
        'name_ar',
        'name_en',
        'is_bilingual',
        'is_ar_only',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_bilingual' => 'boolean',
            'is_ar_only' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FormCategory::class, 'category_code', 'code');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
