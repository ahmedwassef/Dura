<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nationality extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
