<?php

namespace App\Models\Docx;

class Paragraph {
    /**
     * @param Run[] $runs
     */
    public function __construct(
        private ?ParagraphProps $properties = null,
        private array $runs = []
    ) {}

    public function getProperties(): ?ParagraphProps
    {
        return $this->properties;
    }

    /**
     * @return Run[]
     */
    public function getRuns(): array
    {
        return $this->runs;
    }

    public function getText(): string
    {
        return implode("", array_map(fn(Run $run) => $run->getText(), $this->runs));
    }
}
