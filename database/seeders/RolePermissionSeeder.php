<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

final class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Limpia permisos anteriores para recrear con granularidad real
        Permission::query()->where('guard_name', 'web')->delete();

        // ── Permisos por módulo real del sistema ──────────────────────
        $permissions = [

            // ── CLÍNICA ──────────────────────────────────────────────
            'pacientes.view',
            'pacientes.create',
            'pacientes.update',
            'pacientes.delete',
            'pacientes.print',
            'pacientes.export',

            'profesionales.view',
            'profesionales.create',
            'profesionales.update',
            'profesionales.delete',

            // ── AGENDA ───────────────────────────────────────────────
            'turnos.view',
            'turnos.create',
            'turnos.update',
            'turnos.delete',
            'turnos.print',
            'turnos.export',

            'calendario.view',

            // ── HISTORIA CLÍNICA ─────────────────────────────────────
            // Sin delete: los registros médicos no se eliminan
            'consultas.view',
            'consultas.create',
            'consultas.update',
            'consultas.print',
            'consultas.export',

            // ── REPORTES ─────────────────────────────────────────────
            'reportes.view',
            'reportes.print',
            'reportes.export',

            // ── CONFIGURACIÓN › EMPRESA ──────────────────────────────
            'empresa.view',
            'empresa.update',

            'sucursales.view',
            'sucursales.create',
            'sucursales.update',
            'sucursales.delete',

            'departamentos.view',
            'departamentos.create',
            'departamentos.update',
            'departamentos.delete',

            'estructura.view',

            // ── CONFIGURACIÓN › USUARIOS Y SEGURIDAD ─────────────────
            'usuarios.view',
            'usuarios.create',
            'usuarios.update',
            'usuarios.delete',

            'roles.view',
            'roles.manage',

            'menu-acceso.view',
            'menu-acceso.manage',

            'auditoria.view',
            'auditoria.export',

            // ── MAESTROS › OBRAS SOCIALES ────────────────────────────
            'obras-sociales.view',
            'obras-sociales.create',
            'obras-sociales.update',
            'obras-sociales.delete',

            'planes.view',
            'planes.create',
            'planes.update',
            'planes.delete',

            'coberturas.view',
            'coberturas.create',
            'coberturas.update',
            'coberturas.delete',

            // ── CONFIGURACIÓN › PARÁMETROS ────────────────────────────
            'parametros.view',
            'parametros.update',

            'secuencias.view',
            'secuencias.update',

            // ── CONFIGURACIÓN › INTEGRACIONES ────────────────────────
            'integraciones.view',
            'integraciones.update',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles médicos ─────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $medico = Role::firstOrCreate(['name' => 'medico', 'guard_name' => 'web']);
        $recepcionista = Role::firstOrCreate(['name' => 'recepcionista', 'guard_name' => 'web']);
        $enfermero = Role::firstOrCreate(['name' => 'enfermero', 'guard_name' => 'web']);

        // Super Admin — todos los permisos
        $superAdmin->syncPermissions(Permission::all());

        // Admin — configuración completa + maestros + vista clínica + reportes
        $admin->syncPermissions([
            'empresa.view', 'empresa.update',
            'sucursales.view', 'sucursales.create', 'sucursales.update', 'sucursales.delete',
            'departamentos.view', 'departamentos.create', 'departamentos.update', 'departamentos.delete',
            'estructura.view',
            'usuarios.view', 'usuarios.create', 'usuarios.update', 'usuarios.delete',
            'roles.view', 'roles.manage',
            'menu-acceso.view', 'menu-acceso.manage',
            'auditoria.view', 'auditoria.export',
            'parametros.view', 'parametros.update',
            'secuencias.view', 'secuencias.update',
            'integraciones.view', 'integraciones.update',
            'obras-sociales.view', 'obras-sociales.create', 'obras-sociales.update', 'obras-sociales.delete',
            'planes.view', 'planes.create', 'planes.update', 'planes.delete',
            'coberturas.view', 'coberturas.create', 'coberturas.update', 'coberturas.delete',
            'pacientes.view', 'pacientes.export',
            'profesionales.view',
            'turnos.view', 'turnos.export',
            'reportes.view', 'reportes.print', 'reportes.export',
        ]);

        // Médico — clínica completa + consulta obras sociales, sin configuración
        $medico->syncPermissions([
            'pacientes.view', 'pacientes.create', 'pacientes.update', 'pacientes.print',
            'profesionales.view',
            'turnos.view', 'turnos.create', 'turnos.update', 'turnos.print',
            'calendario.view',
            'consultas.view', 'consultas.create', 'consultas.update', 'consultas.print',
            'obras-sociales.view',
            'planes.view',
            'coberturas.view',
            'reportes.view', 'reportes.print',
        ]);

        // Recepcionista — agenda, pacientes + consulta obras sociales para registro
        $recepcionista->syncPermissions([
            'pacientes.view', 'pacientes.create', 'pacientes.update',
            'profesionales.view',
            'turnos.view', 'turnos.create', 'turnos.update', 'turnos.delete', 'turnos.print',
            'calendario.view',
            'obras-sociales.view',
            'planes.view',
            'coberturas.view',
        ]);

        // Enfermero — historia clínica y pacientes, sin agenda ni config
        $enfermero->syncPermissions([
            'pacientes.view',
            'consultas.view', 'consultas.create', 'consultas.update',
        ]);
    }
}
