<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // DASHBOARD
        // ========================================
        Menu::create([
            'title' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard',
            'order' => 1,
            'is_active' => true,
        ]);

        // ========================================
        // MÓDULO: CLÍNICA
        // ========================================
        $clinica = Menu::create([
            'title' => 'CLÍNICA',
            'icon' => 'user-group',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        $pacientes = Menu::create([
            'parent_id' => $clinica->id,
            'title' => 'Pacientes',
            'icon' => 'users',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $pacientes->id,
            'title' => 'Listado de Pacientes',
            'icon' => 'queue-list',
            'route' => 'clinica.pacientes.index',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $pacientes->id,
            'title' => 'Nuevo Paciente',
            'icon' => 'user-plus',
            'route' => 'clinica.pacientes.create',
            'order' => 2,
            'is_active' => false,
        ]);

        $profesionales = Menu::create([
            'parent_id' => $clinica->id,
            'title' => 'Profesionales',
            'icon' => 'academic-cap',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $profesionales->id,
            'title' => 'Listado de Profesionales',
            'icon' => 'queue-list',
            'route' => 'clinica.profesionales.index',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $profesionales->id,
            'title' => 'Nuevo Profesional',
            'icon' => 'user-plus',
            'route' => 'clinica.profesionales.create',
            'order' => 2,
            'is_active' => false,
        ]);

        // ========================================
        // MÓDULO: AGENDA
        // ========================================
        $agenda = Menu::create([
            'title' => 'AGENDA',
            'icon' => 'calendar-days',
            'route' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        $turnos = Menu::create([
            'parent_id' => $agenda->id,
            'title' => 'Turnos',
            'icon' => 'clock',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $turnos->id,
            'title' => 'Ver Agenda',
            'icon' => 'calendar',
            'route' => 'agenda.index',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $turnos->id,
            'title' => 'Nuevo Turno',
            'icon' => 'plus-circle',
            'route' => 'agenda.create',
            'order' => 2,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $agenda->id,
            'title' => 'Calendario',
            'icon' => 'calendar-days',
            'route' => 'agenda.calendario',
            'order' => 2,
            'is_active' => false,
        ]);

        // ========================================
        // MÓDULO: HISTORIA CLÍNICA
        // ========================================
        $historiaClinica = Menu::create([
            'title' => 'HISTORIA CLÍNICA',
            'icon' => 'clipboard-document-list',
            'route' => null,
            'order' => 4,
            'is_active' => true,
        ]);

        $consultas = Menu::create([
            'parent_id' => $historiaClinica->id,
            'title' => 'Consultas',
            'icon' => 'document-text',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $consultas->id,
            'title' => 'Listado de Consultas',
            'icon' => 'queue-list',
            'route' => 'hc.consultas.index',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $consultas->id,
            'title' => 'Nueva Consulta',
            'icon' => 'plus-circle',
            'route' => 'hc.consultas.create',
            'order' => 2,
            'is_active' => false,
        ]);

        // ========================================
        // MÓDULO: REPORTES
        // ========================================
        $reportes = Menu::create([
            'title' => 'REPORTES',
            'icon' => 'chart-bar',
            'route' => null,
            'order' => 5,
            'is_active' => true,
        ]);

        $reportesClinicos = Menu::create([
            'parent_id' => $reportes->id,
            'title' => 'Reportes Clínicos',
            'icon' => 'document-chart-bar',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $reportesClinicos->id,
            'title' => 'Pacientes Atendidos',
            'icon' => 'users',
            'route' => 'reportes.pacientes',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $reportesClinicos->id,
            'title' => 'Turnos por Período',
            'icon' => 'calendar-days',
            'route' => 'reportes.turnos',
            'order' => 2,
            'is_active' => false,
        ]);

        // ========================================
        // MÓDULO: CONFIGURACIÓN
        // ========================================
        $configuracion = Menu::create([
            'title' => 'CONFIGURACIÓN',
            'icon' => 'cog-8-tooth',
            'route' => null,
            'order' => 6,
            'is_active' => true,
        ]);

        // Submódulo: Empresa
        $empresa = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Empresa',
            'icon' => 'building-office',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $empresa->id,
            'title' => 'Datos de la Empresa',
            'icon' => 'identification',
            'route' => 'empresa.datos',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $empresa->id,
            'title' => 'Sucursales',
            'icon' => 'building-storefront',
            'route' => 'empresa.sucursal',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $empresa->id,
            'title' => 'Departamentos',
            'icon' => 'building-library',
            'route' => 'empresa.departamentos',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $empresa->id,
            'title' => 'Estructura Organizacional',
            'icon' => 'chart-bar-square',
            'route' => 'empresa.estructura',
            'order' => 4,
            'is_active' => false,
        ]);

        // Submódulo: Usuarios y Seguridad
        $usuariosSeguridad = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Usuarios y Seguridad',
            'icon' => 'shield-check',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Usuarios',
            'icon' => 'users',
            'route' => 'admin.usuarios.index',
            'order' => 1,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Roles y Permisos',
            'icon' => 'key',
            'route' => 'admin.roles.index',
            'order' => 2,
            'is_active' => false,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Acceso al Menú',
            'icon' => 'bars-3',
            'route' => 'admin.menu-access.index',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Logs de Auditoría',
            'icon' => 'document-magnifying-glass',
            'route' => 'admin.logs.index',
            'order' => 4,
            'is_active' => false,
        ]);

        // Submódulo: Parámetros
        $parametros = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Parámetros',
            'icon' => 'adjustments-horizontal',
            'route' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $parametros->id,
            'title' => 'Parámetros Regionales',
            'icon' => 'map-pin',
            'route' => 'empresa.parametroregional',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $parametros->id,
            'title' => 'Configuración General',
            'icon' => 'cog-6-tooth',
            'route' => 'empresa.configuracion',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $parametros->id,
            'title' => 'Secuencias de Código',
            'icon' => 'hashtag',
            'route' => 'parametros.secuencias',
            'order' => 3,
            'is_active' => true,
        ]);

        // Submódulo: Integraciones
        $integraciones = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Integraciones',
            'icon' => 'link',
            'route' => null,
            'order' => 4,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $integraciones->id,
            'title' => 'WhatsApp',
            'icon' => 'chat-bubble-left-right',
            'route' => 'integraciones.whatsapp',
            'order' => 1,
            'is_active' => false,
        ]);
    }
}
