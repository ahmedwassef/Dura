<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'status',
        'description',
        'sort_order',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function formTemplates(): HasMany
    {
        return $this->hasMany(FormTemplate::class);
    }
}
