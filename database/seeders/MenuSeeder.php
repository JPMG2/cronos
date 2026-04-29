<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

final class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // MÓDULO: OBRAS
        // ========================================
        $obras = Menu::create([
            'title' => 'OBRAS',
            'icon' => 'building-office-2',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $gestionObras = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Gestión de Obras',
            'icon' => 'cog-6-tooth',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $gestionObras->id,
            'title' => 'Listado de Obras',
            'icon' => 'list-bullet',
            'route' => 'obras.index',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $gestionObras->id,
            'title' => 'Obra Interna',
            'icon' => 'home',
            'route' => 'obras.interna',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $gestionObras->id,
            'title' => 'Actualizar Obra',
            'icon' => 'pencil-square',
            'route' => 'obras.edit',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $gestionObras->id,
            'title' => 'Auditar Obra',
            'icon' => 'magnifying-glass',
            'route' => 'auditorias.obra',
            'order' => 4,
            'is_active' => true,
        ]);

        $ordenesTrabajo = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Órdenes de Trabajo',
            'icon' => 'document-text',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $ordenesTrabajo->id,
            'title' => 'Nueva Orden',
            'icon' => 'plus-circle',
            'route' => 'ordenes.create',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $ordenesTrabajo->id,
            'title' => 'Listado de Órdenes',
            'icon' => 'queue-list',
            'route' => 'ordenes.index',
            'order' => 2,
            'is_active' => true,
        ]);

        // Submódulo: Producción
        $produccion = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Producción',
            'icon' => 'cog',
            'route' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $produccion->id,
            'title' => 'Planificación',
            'icon' => 'calendar-days',
            'route' => 'produccion.planificacion',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $produccion->id,
            'title' => 'Hoja de producción',
            'icon' => 'clipboard-document-list',
            'route' => 'produccion.ordenes',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $produccion->id,
            'title' => 'Carga de Producción',
            'icon' => 'arrow-path',
            'route' => 'produccion.carga',
            'order' => 3,
            'is_active' => true,
        ]);

        // Submódulo: Control de Calidad
        $controlCalidad = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Control de Calidad',
            'icon' => 'shield-check',
            'route' => null,
            'order' => 4,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $controlCalidad->id,
            'title' => 'Registro de Probeta',
            'icon' => 'beaker',
            'route' => 'calidad.probeta.registro',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $controlCalidad->id,
            'title' => 'Control de Probeta',
            'icon' => 'clipboard-document-check',
            'route' => 'calidad.probeta.control',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $controlCalidad->id,
            'title' => 'Liberación de Molde',
            'icon' => 'check-circle',
            'route' => 'calidad.molde.liberacion',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $controlCalidad->id,
            'title' => 'Acta de Inspección',
            'icon' => 'document-text',
            'route' => 'calidad.inspeccion',
            'order' => 4,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $controlCalidad->id,
            'title' => 'Hoja de Calidad',
            'icon' => 'document-check',
            'route' => 'calidad.hoja',
            'order' => 5,
            'is_active' => true,
        ]);

        // Submódulo: Ventas
        $ventas = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Ventas',
            'icon' => 'currency-dollar',
            'route' => null,
            'order' => 5,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $ventas->id,
            'title' => 'Registro de Venta',
            'icon' => 'arrow-path-rounded-square',
            'route' => 'registro.venta',
            'order' => 1,
            'is_active' => true,
        ]);

        // Submódulo: Logística y Despacho
        $logisticaDespacho = Menu::create([
            'parent_id' => $obras->id,
            'title' => 'Logística y Despacho',
            'icon' => 'truck',
            'route' => null,
            'order' => 6,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $logisticaDespacho->id,
            'title' => 'Turno de Carga',
            'icon' => 'clock',
            'route' => 'logistica.turno',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $logisticaDespacho->id,
            'title' => 'Orden de Carga',
            'icon' => 'document-duplicate',
            'route' => 'logistica.orden',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $logisticaDespacho->id,
            'title' => 'Control de Despacho',
            'icon' => 'clipboard-document-list',
            'route' => 'logistica.despacho',
            'order' => 3,
            'is_active' => true,
        ]);

        // ========================================
        // MÓDULO: REPORTES Y ANÁLISIS
        // ========================================
        $reportes = Menu::create([
            'title' => 'REPORTES Y ANÁLISIS',
            'icon' => 'chart-bar',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        $reportesOperativos = Menu::create([
            'parent_id' => $reportes->id,
            'title' => 'Reportes Operativos',
            'icon' => 'document-chart-bar',
            'route' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $reportesOperativos->id,
            'title' => 'Avance de Obra',
            'icon' => 'arrow-trending-up',
            'route' => 'reportes.avance',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $reportesOperativos->id,
            'title' => 'Diagrama de Obra',
            'icon' => 'chart-pie',
            'route' => 'reportes.diagrama',
            'order' => 2,
            'is_active' => true,
        ]);

        $auditoriasControl = Menu::create([
            'parent_id' => $reportes->id,
            'title' => 'Auditorías y Control',
            'icon' => 'shield-check',
            'route' => null,
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $auditoriasControl->id,
            'title' => 'Auditar Obra',
            'icon' => 'magnifying-glass',
            'route' => 'auditorias.obra',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $auditoriasControl->id,
            'title' => 'Control de Obras',
            'icon' => 'clipboard-document-check',
            'route' => 'auditorias.control',
            'order' => 2,
            'is_active' => true,
        ]);

        // ========================================
        // MÓDULO: MAESTROS
        // ========================================
        $maestros = Menu::create([
            'title' => 'MAESTROS',
            'icon' => 'cube',
            'route' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $maestros->id,
            'title' => 'Productos',
            'icon' => 'cube-transparent',
            'route' => 'productos.index',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $maestros->id,
            'title' => 'Actividades',
            'icon' => 'clipboard-document-list',
            'route' => 'actividades.index',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $maestros->id,
            'title' => 'Materiales',
            'icon' => 'archive-box',
            'route' => 'materiales.index',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $maestros->id,
            'title' => 'Sectores',
            'icon' => 'squares-2x2',
            'route' => 'sectores.index',
            'order' => 4,
            'is_active' => true,
        ]);

        // ========================================
        // MÓDULO: CONFIGURACIÓN
        // ========================================
        $configuracion = Menu::create([
            'title' => 'CONFIGURACIÓN',
            'icon' => 'cog-8-tooth',
            'route' => null,
            'order' => 4,
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
            'title' => 'Datos Sucursal',
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
            'is_active' => true,
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
            'route' => 'usuarios.index',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Roles y Permisos',
            'icon' => 'key',
            'route' => 'roles.index',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Logs de Auditoría',
            'icon' => 'document-magnifying-glass',
            'route' => 'logs.index',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $usuariosSeguridad->id,
            'title' => 'Sesiones Activas',
            'icon' => 'computer-desktop',
            'route' => 'sesiones.index',
            'order' => 4,
            'is_active' => true,
        ]);

        // Submódulo: Integraciones
        $integraciones = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Integraciones',
            'icon' => 'link',
            'route' => null,
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $integraciones->id,
            'title' => 'Configuración SAP',
            'icon' => 'server',
            'route' => 'integraciones.sap',
            'order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $integraciones->id,
            'title' => 'Configuración WhatsApp',
            'icon' => 'chat-bubble-left-right',
            'route' => 'integraciones.whatsapp',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $integraciones->id,
            'title' => 'Configuración n8n',
            'icon' => 'arrows-right-left',
            'route' => 'integraciones.n8n',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $integraciones->id,
            'title' => 'Estado de APIs',
            'icon' => 'signal',
            'route' => 'integraciones.status',
            'order' => 4,
            'is_active' => true,
        ]);

        // Submódulo: Parámetros
        $parametros = Menu::create([
            'parent_id' => $configuracion->id,
            'title' => 'Parámetros',
            'icon' => 'adjustments-horizontal',
            'route' => null,
            'order' => 4,
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
            'title' => 'Parámetros del Sistema',
            'icon' => 'wrench-screwdriver',
            'route' => 'empresa.parametros',
            'order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $parametros->id,
            'title' => 'Configuración General',
            'icon' => 'cog-6-tooth',
            'route' => 'empresa.configuracion',
            'order' => 3,
            'is_active' => true,
        ]);

        Menu::create([
            'parent_id' => $parametros->id,
            'title' => 'Secuencias de Código',
            'icon' => 'hashtag',
            'route' => 'parametros.secuencias',
            'order' => 4,
            'is_active' => false,
        ]);

    }
}
