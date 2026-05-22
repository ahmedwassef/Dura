<?php

use Illuminate\Support\Facades\Route;

/*
| Legacy /clinic URLs — redirect to /admin/clinic
*/
Route::redirect('/clinic', '/admin/clinic');
Route::redirect('/clinic/admin', '/admin/clinic/records');

Route::get('/clinic/{path}', function (string $path) {
    return redirect('/dashboard/clinic/'.$path);
})->where('path', '.*');
