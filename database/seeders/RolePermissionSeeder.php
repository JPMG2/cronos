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

        // ── Permisos agrupados por módulo ─────────────────────────────
        $permissions = [
            // Configuración
            'configuracion.ver',
            'configuracion.editar',

            // Empresa
            'empresa.ver',
            'empresa.editar',

            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Roles
            'roles.ver',
            'roles.gestionar',

            // Parámetros / Maestros
            'parametros.ver',
            'parametros.editar',

            // Pacientes
            'pacientes.ver',
            'pacientes.crear',
            'pacientes.editar',
            'pacientes.eliminar',

            // Profesionales
            'profesionales.ver',
            'profesionales.crear',
            'profesionales.editar',
            'profesionales.eliminar',

            // Agenda
            'agenda.ver',
            'agenda.crear',
            'agenda.editar',
            'agenda.eliminar',

            // Historia Clínica
            'historia-clinica.ver',
            'historia-clinica.crear',
            'historia-clinica.editar',

            // Reportes
            'reportes.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles médicos ─────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $medico = Role::firstOrCreate(['name' => 'medico', 'guard_name' => 'web']);
        $recepcionista = Role::firstOrCreate(['name' => 'recepcionista', 'guard_name' => 'web']);
        $enfermero = Role::firstOrCreate(['name' => 'enfermero', 'guard_name' => 'web']);

        // Super Admin — todos los permisos
        $superAdmin->syncPermissions(Permission::all());

        // Admin — configuración + usuarios + empresa + parámetros + reportes
        $admin->syncPermissions([
            'configuracion.ver', 'configuracion.editar',
            'empresa.ver', 'empresa.editar',
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'roles.ver', 'roles.gestionar',
            'parametros.ver', 'parametros.editar',
            'pacientes.ver',
            'profesionales.ver',
            'agenda.ver',
            'reportes.ver',
        ]);

        // Médico — clínica completa, sin configuración
        $medico->syncPermissions([
            'pacientes.ver', 'pacientes.crear', 'pacientes.editar',
            'profesionales.ver',
            'agenda.ver', 'agenda.crear', 'agenda.editar',
            'historia-clinica.ver', 'historia-clinica.crear', 'historia-clinica.editar',
            'reportes.ver',
        ]);

        // Recepcionista — agenda y registro de pacientes, sin historia clínica
        $recepcionista->syncPermissions([
            'pacientes.ver', 'pacientes.crear', 'pacientes.editar',
            'profesionales.ver',
            'agenda.ver', 'agenda.crear', 'agenda.editar', 'agenda.eliminar',
        ]);

        // Enfermero — historia clínica y pacientes, sin agenda ni config
        $enfermero->syncPermissions([
            'pacientes.ver',
            'historia-clinica.ver', 'historia-clinica.crear', 'historia-clinica.editar',
        ]);
    }
}
