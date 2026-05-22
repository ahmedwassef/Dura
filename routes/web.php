<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\DashboardController;

Route::redirect('/', '/dashboard/clinic')->name('home');

Route::inertia('/welcome', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('welcome');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/clinic.php';
require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
