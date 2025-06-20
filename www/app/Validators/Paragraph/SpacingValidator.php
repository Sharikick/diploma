<?php

namespace App\Validators\Paragraph;

use App\Models\Docx\Error;
use App\Models\Docx\Paragraph;
use App\Models\Docx\ParagraphProps;

class SpacingValidator implements ParagraphValidatorInterface
{
    public function __construct(
        private array $global_styles,
        private array $styles
    ) {}

    public function validate(Paragraph $paragraph): array
    {
        $errors = [];
        if (empty($paragraph->getRuns()) && empty($paragraph->getText())) return $errors;

        $pPr = $paragraph->getProperties();

        $spacing = $this->getSpacing($pPr);

        if ($spacing["line"] != 360 || !in_array($spacing["rule"], ['auto', 'atLeast'])) {
            $errors[] = new Error(
                location: "Основной текст",
                type: "spacing",
                message: "Неверный межстрочный интервал. Ожидается полуторный (360 твипов, auto или atLeast).",
                context: [
                    'actual_spacing' => $spacing["line"],
                    'paragraph_text' => $paragraph->getText()
                ]
            );
        }

        return $errors;
    }

    private function getSpacing(?ParagraphProps $pPr): ?array
    {
        if ($pPr && $pPr->getSpacing() && isset($pPr->getSpacing()["line"]) && isset($pPr->getSpacing()["rule"])) {
            return $pPr->getSpacing();
        }

        $styleId = $pPr->getStyle();
        if ($styleId && $this->styles[$styleId] && $this->styles[$styleId]["pPr"] && $this->styles[$styleId]["pPr"]->getSpacing() && isset($this->styles[$styleId]["pPr"]->getSpacing()["line"]) && isset($this->styles[$styleId]["pPr"]->getSpacing()["rule"])) {
            return $this->styles[$styleId]["pPr"]->getSpacing();
        }

        if ($this->global_styles["pPr"] && $this->global_styles["pPr"]->getSpacing() && isset($this->global_styles["pPr"]->getSpacing()["line"]) && isset($this->global_styles["pPr"]->getSpacing()["rule"])) {
            return $this->global_styles["pPr"]->getSpacing();
        }

        return null;
    }
}
