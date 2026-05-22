<?php

namespace App\Contracts\Repositories;

use App\Models\FormTemplate;

interface FormTemplateRepositoryInterface
{
    /**
     * Find a form template by its code.
     *
     * @param string $code
     * @return FormTemplate|null
     */
    public function findByCode(string $code): ?FormTemplate;

    /**
     * Get all form templates.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(): \Illuminate\Support\Collection;
}
