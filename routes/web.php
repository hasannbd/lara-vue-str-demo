<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

//Route::get('dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Role routes
    Route::get('/roles', [RoleController::class, 'index'])->name('role.index')->middleware('permission:role.view');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create')->middleware('permission:role.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store')->middleware('permission:role.create');
    Route::get('/role/show/{id}', [RoleController::class, 'show'])->name('role.show')->middleware('permission:role.view');
    Route::get('/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit')->middleware('permission:role.update');
    Route::put('/role/update/{id}', [RoleController::class, 'update'])->name('role.update')->middleware('permission:role.update');
    Route::delete('/role/delete/{id}', [RoleController::class, 'delete'])->name('role.delete')->middleware('permission:role.delete');
    // User routes
    Route::get('/users', [UserController::class, 'index'])->name('user.index')->middleware('permission:user.view');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store')->middleware('permission:user.create');
    Route::get('/user/show/{id}', [UserController::class, 'show'])->name('user.show')->middleware('permission:user.view');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:user.update');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update')->middleware('permission:user.update');
    Route::delete('/user/delete/{id}', [UserController::class, 'delete'])->name('user.delete')->middleware('permission:user.delete');
    // Permission routes
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permission.index')->middleware('permission:permission.view');
    Route::get('/permission/show/{id}', [PermissionController::class, 'show'])->name('permission.show')->middleware('permission:permission.view');
    Route::delete('/permission/delete/{id}', [PermissionController::class, 'delete'])->name('permission.delete')->middleware('permission:permission.delete');
});

require __DIR__.'/settings.php';
