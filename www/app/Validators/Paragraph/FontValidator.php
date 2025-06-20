<?php

namespace App\Validators\Paragraph;

use App\Models\Docx\Error;
use App\Models\Docx\Paragraph;
use App\Models\Docx\ParagraphProps;
use App\Models\Docx\RunProps;

class FontValidator implements ParagraphValidatorInterface
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
        if (empty($paragraph->getRuns()) && empty($paragraph->getText())) return $errors;

        $pPr = $paragraph->getProperties();

        $cache = [
            "font" => null,
            "text" => ""
        ];

        foreach ($paragraph->getRuns() as $run) {
            $font = $this->getFont($run->getProperties(), $pPr);

            if ($font === null) {
                if ($cache["font"] === null) {
                    $cache["text"] .= $run->getText();
                } else {
                    if ($cache["font"] !== "Times New Roman") {
                        $errors[] = new Error(
                            location: "Основной текст",
                            type: "font",
                            message: "Неверный шрифт. Ожидается Times New Roman.",
                            context: [
                                'actual_font' => $cache["font"],
                                'run_text' => $cache["text"],
                                'paragraph_text' => $paragraph->getText()
                            ]
                        );
                    }

                    $cache["font"] = $font;
                    $cache["text"] = $run->getText();
                }
            } elseif ($font !== $cache["font"]) {
                if (!empty($cache["text"] && $cache["font"] !== "Times New Roman")) {
                    $errors[] = new Error(
                        location: "Основной текст",
                        type: "font",
                        message: "Неверный шрифт. Ожидается Times New Roman.",
                        context: [
                            'actual_font' => $cache["font"],
                            'run_text' => $cache["text"],
                            'paragraph_text' => $paragraph->getText()
                        ]
                    );
                }

                $cache["font"] = $font;
                $cache["text"] = $run->getText();
            } else {
                $cache["text"] .= $run->getText();
            }

        }

        if ($cache["font"] !== "Times New Roman" && !empty($cache["text"])) {
            $errors[] = new Error(
                location: "Основной текст",
                type: "font",
                message: "Неверный шрифт. Ожидается Times New Roman.",
                context: [
                    'actual_font' => $cache["font"],
                    'run_text' => $cache["text"],
                    'paragraph_text' => $paragraph->getText()
                ]
            );
        }

        return $errors;
    }

    private function getFont(?RunProps $rPr, ?ParagraphProps $pPr): ?string
    {
        if ($rPr && $rPr->getFont()) return $rPr->getFont();

        if ($pPr && $pPr->getRunProps()?->getFont()) return $pPr->getRunProps()->getFont();

        $styleId = $pPr->getStyle();
        if ($styleId &&
            isset($this->styles[$styleId]) &&
            $this->styles[$styleId]["rPr"] &&
            $this->styles[$styleId]["rPr"]?->getFont()
        ) return $this->styles[$styleId]["rPr"]?->getFont();

        return $this->global_styles["rPr"]?->getFont() ?? null;
    }
}
