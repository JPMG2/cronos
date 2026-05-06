<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**Configuracion/Empresa**/
    Route::livewire('/configuracion/company', 'configuracion.empresa.create-company')->name('empresa.datos');
    Route::livewire('/configuracion/branch', 'configuracion.empresa.create-branch')
        ->name('empresa.sucursal')
        ->middleware('company.exists');
    Route::livewire('/configuracion/department', 'configuracion.empresa.create-department')
        ->name('empresa.departamentos')
        ->middleware('company.exists')
        ->middleware('branch.exists');

    Route::livewire('/configuracion/regional', 'configuracion.parametros.create-region')->name('empresa.parametroregional');

    /**Configuracion/Parámetros**/
    Route::livewire('/configuracion/codesequence', 'configuracion.parametros.code-sequence')->name('parametros.secuencias');
    Route::livewire('/configuracion/generalconf', 'configuracion.parametros.general-configuration')->name('empresa.configuracion');
});

require __DIR__ . '/auth.php';
