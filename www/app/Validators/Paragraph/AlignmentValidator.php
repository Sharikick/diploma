<?php

namespace App\Validators\Paragraph;

use App\Models\Docx\Error;
use App\Models\Docx\Paragraph;
use App\Models\Docx\ParagraphProps;

class AlignmentValidator implements ParagraphValidatorInterface
{
    public function __construct(
        private array $global_styles,
        private array $styles
    ) {}

    public function validate(Paragraph $paragraph): array
    {
        $errors = [];
        if (empty($paragraph->getText()) || empty($paragraph->getRuns())) return $errors;

        $pPr = $paragraph->getProperties();

        $styleId = $pPr->getStyle();
        if ($styleId === null) {
            $styleId = "Normal";
        }

        $expected = $this->getExpectedAlignment($styleId);

        $alignment = $this->getAlignment($pPr);

        if ($alignment !== $expected) {
            $errors[] = new Error(
                location: "Основной текст",
                type: "alignment",
                message: "Неверное выравнивание. Ожидается '{$expected}'.",
                context: [
                    'actual_alignment' => $alignment,
                    'paragraph_text' => $paragraph->getText(),
                ]
            );
        }

        return $errors;
    }

    private function getAlignment(?ParagraphProps $pPr): ?string
    {
        if ($pPr && $pPr->getAlignment()) {
            return $pPr->getAlignment();
        }

        $styleId = $pPr?->getStyle();
        if ($styleId && isset($this->styles[$styleId]) && $this->styles[$styleId]["pPr"] && $this->styles[$styleId]["pPr"]->getAlignment()) {
            return $this->styles[$styleId]["pPr"]->getAlignment();
        }

        if ($this->global_styles["pPr"] && $this->global_styles["pPr"]->getAlignment()) {
            return $this->global_styles["pPr"]->getAlignment();
        }

        return null;
    }

    private function getExpectedAlignment(string $styleId): string
    {
        if ($this->isHeading($styleId))
        {
            return "center";
        }

        return "both";
    }

    private function isHeading(string $styleId): bool
    {
        $keywords = ['heading', 'head', 'заголовок'];

        foreach ($keywords as $keyword) {
            if (str_contains(strtolower($styleId), $keyword)) {
                return true;
            }
        }

        return false;

    }
}
