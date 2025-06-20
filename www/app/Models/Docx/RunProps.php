<?php

namespace App\Models\Docx;

class RunProps {
    public function __construct(
        private ?string $font = null,
        private ?int $size = null,
        private ?int $sizeComplex = null,
        private bool $italic = false,
        private bool $bold = false
    ) {}

    public function merge(self $other): self
    {
        return new self(
            font: $this->font ?? $other->getFont(),
            size: $this->size ?? $other->getFontSize(),
            sizeComplex: $this->sizeComplex ?? $other->getFontSizeComplex(),
            italic: $this->italic ?? $other->getItalic(),
            bold: $this->bold ?? $other->getBold()
        );
    }

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function getFontSize(): ?int
    {
        return $this->size;
    }

    public function getFontSizeComplex(): ?int
    {
        return $this->sizeComplex;
    }

    public function getItalic(): bool
    {
        return $this->italic;
    }

    public function getBold(): bool
    {
        return $this->bold;
    }
}
