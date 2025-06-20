<?php

namespace App\Validators\Paragraph;

use App\Models\Docx\Error;
use App\Models\Docx\Paragraph;
use App\Models\Docx\ParagraphProps;
use App\Models\Docx\RunProps;

class FontSizeValidator implements ParagraphValidatorInterface
{
    public function __construct(
        private array $global_styles,
        private array $styles
    ) {}

    /**
     * @return Error[]
     */
    public function validate(Paragraph $paragraph): array
    {
        $errors = [];
        if (empty($paragraph->getRuns()) || empty($paragraph->getText())) return $errors;

        $pPr = $paragraph->getProperties();

        $cache = [
            "size" => null,
            "text" => ""
        ];

        foreach($paragraph->getRuns() as $run) {
            $size = $this->getFontSize($run->getProperties(), $pPr);

            if ($size === null) {
                if ($cache["size"] === null) {
                    $cache["text"] .= $run->getText();
                } else {
                    if ($cache["size"] !== 28) {
                        $errors[] = new Error(
                            location: "Основной текст",
                            type: "font_size",
                            message: "Неверный размер шрифта. Ожидается 14pt (28 единиц).",
                            context: [
                                'actual_size' => $cache["size"],
                                'run_text' => $cache["text"],
                                'paragraph_text' => $paragraph->getText()
                            ]
                        );
                    }

                    $cache["size"] = $size;
                    $cache["text"] = $run->getText();
                }
            } elseif ($size !== $cache["size"]) {
                if (!empty($cache["text"]) && $cache["size"] !== 28) {
                    $errors[] = new Error(
                        location: "Основной текст",
                        type: "font_size",
                        message: "Неверный размер шрифта. Ожидается 14pt (28 единиц).",
                        context: [
                            'actual_size' => $cache["size"],
                            'run_text' => $cache["text"],
                            'paragraph_text' => $paragraph->getText()
                        ]
                    );
                }

                $cache["size"] = $size;
                $cache["text"] = $run->getText();
            } else {
                $cache["text"] .= $run->getText();
            }
        }

        if ($cache["size"] !== 28 && !empty($cache["text"])) {
            $errors[] = new Error(
                location: "Основной текст",
                type: "font_size",
                message: "Неверный размер шрифта. Ожидается 14pt (28 единиц).",
                context: [
                    'actual_size' => $cache["size"],
                    'run_text' => $cache["text"],
                    'paragraph_text' => $paragraph->getText()
                ]
            );
        }

        return $errors;
    }

    private function getFontSize(?RunProps $rPr, ?ParagraphProps $pPr): ?int
    {
        if ($rPr && $rPr->getFontSize()) return $rPr->getFontSize();

        if ($pPr && $pPr->getRunProps()?->getFontSize()) return $pPr->getRunProps()->getFontSize();

        $styleId = $pPr->getStyle();
        if ($styleId &&
            isset($this->styles[$styleId]) &&
            $this->styles[$styleId]["rPr"] &&
            $this->styles[$styleId]["rPr"]?->getFontSize()
        ) return $this->styles[$styleId]["rPr"]?->getFontSize();

        return $this->global_styles["rPr"]?->getFontSize() ?? null;
    }
}
