<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormCategory extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'name_ar',
        'name_en',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function formTemplates(): HasMany
    {
        return $this->hasMany(FormTemplate::class, 'category_code', 'code');
    }
}
