<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class NotificationService
{
    public static function getContext(string $tableName): array
    {
        return [
            'users' => ['name' => 'Usuario', 'genero' => 'M'],
            'companies' => ['name' => 'Empresa', 'genero' => 'F'],
            'branches' => ['name' => 'Sucursal', 'genero' => 'F'],
            'departments' => ['name' => 'Departamento', 'genero' => 'M'],
            'sequences' => ['name' => 'Secuencia', 'genero' => 'F'],
        ][mb_strtolower($tableName)] ?? ['name' => 'Registro', 'genero' => 'M'];
    }

    public function sendNotificacion(Model $model, string $action): array
    {
        $actionMap = [
            'create' => 'created',
            'created' => 'created',
            'update' => 'updated',
            'updated' => 'updated',
            'delete' => 'deleted',
            'deleted' => 'deleted',
        ];

        $normalizedAction = $actionMap[$action] ?? null;

        if ($normalizedAction === null) {
            throw new InvalidArgumentException("Accion no permitida: {$action}");
        }

        return $this->{$normalizedAction}($model);
    }

    public function created(Model $model): array
    {
        $message = self::getContext($model->getTable());

        $messageGender = $message['genero'] === 'M' ? 'creado' : 'creada';

        if ($model->wasRecentlyCreated) {
            return [$message['name'] . ' ' . $messageGender . ' correctamente', 'notifySuccess'];
        }

        return ['Registro no creado', 'notifyError'];
    }

    public function updated(Model $model): array
    {
        $message = self::getContext($model->getTable());

        $messageGender = $message['genero'] === 'M' ? 'actualizado' : 'actualizada';

        if ($model->wasChanged()) {
            return [$message['name'] . ' ' . $messageGender . ' correctamente', 'notifySuccess'];
        }

        return ['No se realizaron cambios en el registro.', 'notifyInfo'];
    }

    public function deleted(Model $model): array
    {
        $message = self::getContext($model->getTable());

        $messageGender = $message['genero'] === 'M' ? 'eliminado' : 'eliminada';

        return [$message['name'] . ' ' . $messageGender . ' correctamente', 'notifySuccess'];
    }
}
