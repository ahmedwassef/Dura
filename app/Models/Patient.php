<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

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
        'history',
    ];

    protected function casts(): array
    {
        return [
            'history' => 'array',
        ];
    }

    public function recordChange(array $original, array $updated, ?User $user): void
    {
        $trackedFields = ['name', 'id_number', 'file_number', 'phone', 'nationality_id', 'date_of_birth', 'sex', 'address'];
        $changes = [];

        foreach ($trackedFields as $field) {
            $oldVal = $original[$field] ?? null;
            $newVal = $updated[$field] ?? null;

            // Normalize dates
            if ($field === 'date_of_birth' && $oldVal && $newVal) {
                try {
                    $oldVal = \Carbon\Carbon::parse($oldVal)->format('Y-m-d');
                    $newVal = \Carbon\Carbon::parse($newVal)->format('Y-m-d');
                } catch (\Exception $e) {}
            }

            if ($oldVal != $newVal) {
                $changes[$field] = [
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        if (empty($changes)) {
            return;
        }

        $history = $this->history ?: [];
        $history[] = [
            'user_name' => $user?->name ?: 'System',
            'updated_at' => now()->toIso8601String(),
            'changes' => $changes,
        ];

        $this->history = $history;
    }

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
