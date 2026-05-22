<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface ServiceCatalogRepositoryInterface
{
    public function search(?string $query, int $limit = 12): Collection;
}
