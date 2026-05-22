<?php

namespace App\Models;

use App\Enums\FormSubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FormSubmission extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    public const MEDIA_ARCHIVE_PDF = 'archive_pdf';

    public const MEDIA_ADMIN_STAMP = 'admin_stamp';

    protected $fillable = [
        'uuid',
        'form_template_id',
        'patient_id',
        'doctor_id',
        'branch_id',
        'created_by',
        'updated_by',
        'patient_name',
        'id_number',
        'file_number',
        'doctor_name',
        'language',
        'status',
        'grand_total',
        'is_signed',
        'signed_at',
        'submitted_at',
        'payload',
        'tooth_states',
        'plan_teeth',
        'age_mode',
        'extra_discount',
        'extra_discount_type',
        'specialty_breakdown',
    ];

    protected function casts(): array
    {
        return [
            'status' => FormSubmissionStatus::class,
            'grand_total' => 'decimal:2',
            'is_signed' => 'boolean',
            'signed_at' => 'datetime',
            'submitted_at' => 'datetime',
            'payload' => 'array',
            'tooth_states' => 'array',
            'plan_teeth' => 'array',
            'extra_discount' => 'decimal:2',
            'specialty_breakdown' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FormSubmission $submission): void {
            if (empty($submission->uuid)) {
                $submission->uuid = (string) Str::uuid();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_ARCHIVE_PDF)
            ->singleFile()
            ->useDisk('archive')
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection(self::MEDIA_ADMIN_STAMP)
            ->singleFile()
            ->useDisk('archive')
            ->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/webp']);
    }

    public function archivePdf(): ?Media
    {
        return $this->getFirstMedia(self::MEDIA_ARCHIVE_PDF);
    }

    public function adminStamp(): ?Media
    {
        return $this->getFirstMedia(self::MEDIA_ADMIN_STAMP);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user(): BelongsTo
    {
        return $this->creator();
    }

    public function treatmentPlanItems(): HasMany
    {
        return $this->hasMany(TreatmentPlanItem::class);
    }

    public function signatures(): MorphMany
    {
        return $this->morphMany(Signature::class, 'signable');
    }
}
