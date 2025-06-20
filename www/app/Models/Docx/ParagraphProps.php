<?php

namespace App\Models\Docx;

class ParagraphProps {
    public function __construct(
        private ?string $style = null,
        private ?array $spacing = null,
        private ?string $alignment = null,
        private ?RunProps $runProps = null
    ) {}

    public function merge(?self $other): self
    {
        return new self(
            style: $this->style ?? $other->getStyle(),
            spacing: $this->spacing ?? $other->getSpacing(),
            alignment: $this->alignment ?? $other->getAlignment(),
            runProps: $other ? $this->runProps->merge($other->getRunProps()) : $this->runProps
        );
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function getSpacing(): ?array
    {
        return $this->spacing;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function getRunProps(): ?RunProps
    {
        return $this->runProps;
    }
}
