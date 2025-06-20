<?php

namespace App\Models\Docx;

class Error {
    public function __construct(
        private string $location,
        private string $type,
        private string $message,
        private array $context
    ) {}

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
