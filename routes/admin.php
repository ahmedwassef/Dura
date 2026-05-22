<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FormCategoryController;
use App\Http\Controllers\Admin\FormTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Clinic\ClinicController;
use App\Http\Controllers\Clinic\ClinicSessionController;
use App\Http\Controllers\Clinic\FormSubmissionController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\DoctorController;
use App\Http\Controllers\Clinic\ServiceCatalogController;
use App\Http\Controllers\Clinic\NationalityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormFieldController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->middleware(['auth', 'verified', 'branch.restriction'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('clinic')->name('clinic.')->middleware('clinic.session')->group(function () {
        Route::get('/', [ClinicController::class, 'branchSelect'])->name('branch');
        Route::post('/session/branch', [ClinicSessionController::class, 'storeBranch'])->name('session.branch');
        Route::get('/role', [ClinicController::class, 'roleSelect'])->name('role');
        Route::post('/session/role', [ClinicSessionController::class, 'storeRole'])->name('session.role');
        Route::get('/home', [ClinicController::class, 'home'])->name('home');
        Route::get('/dental', [ClinicController::class, 'dental'])->name('dental');
        Route::get('/dermatology', [ClinicController::class, 'dermatology'])->name('dermatology');
        Route::get('/category/{category}', [ClinicController::class, 'category'])->name('category');

        Route::middleware([])->group(function () {
            Route::get('/archive', [ClinicController::class, 'archive'])->name('archive');
        });

        Route::get('/records', [ClinicController::class, 'records'])->name('records');

        Route::get('/forms/{form}', [ClinicController::class, 'form'])->name('forms.show');

        Route::post('/submissions', [FormSubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/submissions/{uuid}/pdf', [FormSubmissionController::class, 'pdf'])->name('submissions.pdf');
        Route::get('/signatures/{signature}', [FormSubmissionController::class, 'signatureImage'])->name('signatures.image');
        Route::get('/services/search', [FormSubmissionController::class, 'searchServices'])->name('services.search');
        Route::get('/nationalities', [PatientController::class, 'nationalities'])->name('nationalities.index');
        Route::get('/submissions/{uuid}/edit', [ClinicController::class, 'editSubmission'])->name('clinic.submissions.edit');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

        Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');
        Route::resource('doctors', DoctorController::class)->except(['create', 'edit', 'show']);
        Route::resource('services', ServiceCatalogController::class)->except(['create', 'edit', 'show']);
        Route::resource('nationalities', NationalityController::class)->except(['create', 'edit', 'show']);
    });

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show', 'create', 'edit']);
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::resource('branches', BranchController::class)->except(['show', 'create', 'edit']);
    Route::resource('form-categories', FormCategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('form-templates', FormTemplateController::class)->except(['create', 'edit', 'show']);
    Route::get('form-templates/{form_template}/fields', [FormFieldController::class, 'index'])->name('form-templates.fields.index');
    Route::post('form-templates/{form_template}/fields', [FormFieldController::class, 'store'])->name('form-templates.fields.store');
    Route::post('form-templates/{form_template}/fields/reorder', [FormFieldController::class, 'reorder'])->name('form-templates.fields.reorder');
    Route::put('form-fields/{form_field}', [FormFieldController::class, 'update'])->name('form-fields.update');
    Route::delete('form-fields/{form_field}', [FormFieldController::class, 'destroy'])->name('form-fields.destroy');
});
