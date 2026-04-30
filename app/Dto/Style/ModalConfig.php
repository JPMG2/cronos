<?php

declare(strict_types=1);

namespace App\Dto\Style;

final class ModalConfig
{
    public function __construct(
        public string $title,
        public string $message,
        public string $type,
        public array $buttons = [],
    ) {}

    public static function make(...$args): self
    {
        return new self(...$args);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            message: $data['message'],
            type: $data['type'],
            buttons: $data['buttons'] ?? [],
        );
    }
}
