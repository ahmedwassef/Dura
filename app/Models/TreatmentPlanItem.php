<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanItem extends Model
{
    protected $fillable = [
        'form_submission_id',
        'service_code',
        'name_ar',
        'name_en',
        'category',
        'tooth_number',
        'quantity',
        'unit_price',
        'discount',
        'discount_type',
        'line_total',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }
}
