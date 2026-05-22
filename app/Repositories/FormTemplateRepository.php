<?php

namespace App\Repositories;

use App\Contracts\Repositories\FormTemplateRepositoryInterface;
use App\Models\FormTemplate;

class FormTemplateRepository implements FormTemplateRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findByCode(string $code): ?FormTemplate
    {
        return FormTemplate::query()
            ->where('code', $code)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): \Illuminate\Support\Collection
    {
        return FormTemplate::query()->orderBy('name_en')->get();
    }
}
