<?php

namespace App\Repositories;

use App\Contracts\Repositories\ServiceCatalogRepositoryInterface;
use App\Models\ServiceCatalog;
use Illuminate\Support\Collection;

class ServiceCatalogRepository implements ServiceCatalogRepositoryInterface
{
    public function search(?string $query, int $limit = 12): Collection
    {
        $builder = ServiceCatalog::query()
            ->where('is_active', true)
            ->orderBy('code');

        if ($query) {
            $builder->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                    ->orWhere('name_ar', 'like', "%{$query}%")
                    ->orWhere('name_en', 'like', "%{$query}%");
            });
        }

        return $builder->limit($limit)->get();
    }
}
