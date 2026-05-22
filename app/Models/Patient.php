<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'id_number',
        'file_number',
        'phone',
        'nationality_id',
        'date_of_birth',
        'sex',
        'address',
    ];

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
