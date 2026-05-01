<?php

declare(strict_types=1);

namespace App\Traits\Livewire;

trait HasNotifications
{
    public function getTypeMessage(string $message, string $type)
    {
        match ($type) {
            'notifySuccess' => $this->notifySuccess($message),
            'notifyError' => $this->notifyError($message),
            'notifyWarning' => $this->notifyWarning($message),

            default => $this->notifyInfo($message),
        };
    }

    public function notifySuccess(string $message): void
    {
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function notifyError(string $message): void
    {
        $this->dispatch('notify', type: 'error', message: $message);
    }

    public function notifyWarning(string $message): void
    {
        $this->dispatch('notify', type: 'warning', message: $message);
    }

    public function notifyInfo(string $message): void
    {
        $this->dispatch('notify', type: 'info', message: $message);
    }
}
