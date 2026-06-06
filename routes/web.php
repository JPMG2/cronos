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

    // ── Perfil (Breeze — sin restricción de permiso) ──────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Configuración › Empresa ───────────────────────────────────────────────
    Route::livewire('/configuracion/company', 'configuracion.empresa.create-company')
        ->name('empresa.datos')
        ->middleware('permission:empresa.view');

    Route::livewire('/configuracion/branch', 'configuracion.empresa.create-branch')
        ->name('empresa.sucursal')
        ->middleware(['permission:sucursales.view', 'company.exists']);

    Route::livewire('/configuracion/department', 'configuracion.empresa.create-department')
        ->name('empresa.departamentos')
        ->middleware(['permission:departamentos.view', 'company.exists', 'branch.exists']);

    // ── Configuración › Parámetros ────────────────────────────────────────────
    Route::livewire('/configuracion/regional', 'configuracion.parametros.create-region')
        ->name('empresa.parametroregional')
        ->middleware('permission:parametros.view');

    Route::livewire('/configuracion/codesequence', 'configuracion.parametros.code-sequence')
        ->name('parametros.secuencias')
        ->middleware('permission:secuencias.view');

    Route::livewire('/configuracion/generalconf', 'configuracion.parametros.general-configuration')
        ->name('empresa.configuracion')
        ->middleware('permission:parametros.view');

    Route::livewire('/configuracion/tipos-cobertura', 'configuracion.coverage.coverage-type')
        ->name('parametros.tipos-cobertura')
        ->middleware('permission:parametros.view');

    // ── Maestros ─────────────────────────────────────────────────────────────
    Route::livewire('/maestros/obras-sociales', 'master.insurances')
        ->name('maestros.obras-sociales.create')
        ->middleware('permission:obras-sociales.view');

    // ── Admin › Seguridad ─────────────────────────────────────────────────────
    Route::livewire('/admin/roles', 'admin.seguridad.manage-roles')
        ->name('admin.roles.index')
        ->middleware('permission:roles.view');

    Route::livewire('/admin/menu-access', 'admin.seguridad.manage-menu-access')
        ->name('admin.menu-access.index')
        ->middleware('permission:menu-acceso.view');
});

require __DIR__ . '/auth.php';
