<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Signature extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const MEDIA_SIGNATURE = 'signature';

    protected $fillable = [
        'signable_type',
        'signable_id',
        'role',
        'signer_name',
        'image_data',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_SIGNATURE)
            ->singleFile()
            ->useDisk('archive')
            ->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/webp']);
    }

    public function signatureImage(): ?Media
    {
        return $this->getFirstMedia(self::MEDIA_SIGNATURE);
    }

    public function signable(): MorphTo
    {
        return $this->morphTo();
    }
}
