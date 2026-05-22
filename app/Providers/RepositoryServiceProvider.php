<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Repositories\BranchRepositoryInterface::class,
            \App\Repositories\BranchRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\FormTemplateRepositoryInterface::class,
            \App\Repositories\FormTemplateRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\DepartmentRepositoryInterface::class,
            \App\Repositories\DepartmentRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\FormSubmissionRepositoryInterface::class,
            \App\Repositories\FormSubmissionRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\PatientRepositoryInterface::class,
            \App\Repositories\PatientRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\ServiceCatalogRepositoryInterface::class,
            \App\Repositories\ServiceCatalogRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\RoleRepositoryInterface::class,
            \App\Repositories\RoleRepository::class
        );

        $this->app->bind(
            \App\Contracts\Repositories\PermissionRepositoryInterface::class,
            \App\Repositories\PermissionRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
