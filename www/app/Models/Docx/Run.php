<?php

namespace App\Models\Docx;

class Run {
    public function __construct(
        private string $text,
        private ?RunProps $properties = null
    ) {}

    public function getText(): string
    {
        return $this->text;
    }

    public function getProperties(): ?RunProps
    {
        return $this->properties;
    }
}
