<?php

namespace App\Validators;

use App\Models\Docx\Paragraph;
use App\Models\Docx\ParagraphProps;
use App\Models\Docx\Run;
use App\Models\Docx\RunProps;
use App\Types\Attr;
use App\Types\Tag;
use App\Utils\XML;
use XMLReader;

class TextValidator
{
    private array $errors = [];
    private array $validators = [];

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    public function validate(string $xmlContent): array
    {
        $this->errors = [];

        $reader = new XMLReader();
        $reader->XML($xmlContent);
        $depth = 0;

        while ($reader->read())
        {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === Tag::body->value) {
                $depth = $reader->depth;
                continue;
            }

            if ($reader->nodeType === XMLReader::END_ELEMENT) continue;

            if ($reader->depth === $depth + 1)
            {
                switch ($reader->localName)
                {
                    case Tag::p->value:
                        $this->validateParagraph($reader);
                        break;
                }
            }
       }

        $reader->close();
        return $this->errors;
    }

    private function validateParagraph(XMLReader $reader): void
    {
        $paragraph = $this->parseParagraph($reader);

        foreach ($this->validators as $validator)
        {
            $this->errors = array_merge($this->errors, $validator->validate($paragraph));
        }
    }

    public function parseParagraph(XMLReader $reader): Paragraph
    {
        $properties = null;
        $runs = [];

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::p->value) {
                break;
            }

            switch ($reader->localName) {
                case Tag::pPr->value:
                    $properties = $this->parseParagraphProperties($reader);
                    break;

                case Tag::r->value:
                    $run = $this->parseRun($reader);
                    if (!empty($run->getText()))
                    {
                        $runs[] = $run;
                    }
                    break;
            }
        }

        return new Paragraph($properties, $runs);
    }

    private function parseRun(XMLReader $reader): Run
    {
        $text = "";
        $properties = null;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::r->value) {
                break;
            }

            switch ($reader->localName) {
                case Tag::rPr->value:
                    $properties = $this->parseRunProperties($reader);
                    break;

                case Tag::t->value:
                    $text .= $reader->readString();
                    break;
            }
        }

        return new Run($text, $properties);
    }

    private function parseRunProperties(XMLReader $reader): RunProps
    {
        $font = null;
        $size = null;
        $sizeComplex = null;
        $italic = false;
        $bold = false;

        if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === Tag::rPr->value) {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::rPr->value) {
                    break;
                }

                switch ($reader->localName) {
                    case Tag::rFonts->value:
                        $font = XML::getAttr($reader, Attr::ascii);
                        break;

                    case Tag::sz->value:
                        $size = XML::castValue("int", XML::getAttr($reader, Attr::val));
                        break;

                    case Tag::szCs->value:
                        $sizeComplex = XML::castValue("int", XML::getAttr($reader, Attr::val));
                        break;

                    case Tag::i->value:
                        $val = XML::getAttr($reader, Attr::val);
                        $italic = in_array($val, ["1", "on", "true"]);
                        break;

                    case Tag::b->value:
                        $val = XML::getAttr($reader, Attr::val);
                        $bold = in_array($val, ["1", "on", "true"]);
                        break;
                }
            }
        }

        return new RunProps($font, $size, $sizeComplex, $italic, $bold);
    }

    private function parseParagraphProperties(XMLReader $reader): ParagraphProps
    {
        $style = null;
        $spacing = null;
        $alignment = null;
        $runProps = null;

        if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === Tag::pPr->value) {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::pPr->value) {
                    break;
                }

                switch ($reader->localName) {
                    case Tag::pStyle->value:
                        $style = XML::getAttr($reader, Attr::val);

                    case Tag::spacing->value:
                        $spacing = [
                            "line" => XML::castValue("int", XML::getAttr($reader, Attr::line)),
                            "rule" => XML::getAttr($reader, Attr::lineRule)
                        ];
                        break;

                    case Tag::jc->value:
                        $alignment = XML::getAttr($reader, Attr::val);
                        break;

                    case Tag::rPr->value:
                        $runProps = $this->parseRunProperties($reader);
                        break;
                }
            }
        }

        return new ParagraphProps($style, $spacing, $alignment, $runProps);
    }
}
