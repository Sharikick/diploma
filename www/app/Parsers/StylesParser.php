<?php

namespace App\Parsers;

use App\Models\Docx\ParagraphProps;
use App\Models\Docx\RunProps;
use App\Types\Attr;
use App\Types\Tag;
use App\Utils\XML;
use XMLReader;

class StylesParser
{
    public function parse(string $xmlContent): array
    {
        $content = [
            "global_styles" => [],
            "styles" => [],
        ];

        $reader = new XMLReader();
        $reader->XML($xmlContent);

        while ($reader->read()) {
            switch ($reader->localName) {
                case Tag::docDefaults->value:
                    $content["global_styles"] = $this->parseGlobalStyles($reader);
                    break;

                case Tag::style->value:
                    $styleId = XML::getAttr($reader, Attr::styleId);
                    if ($styleId) {
                        $content["styles"][$styleId] = $this->parseStyle($reader);
                    }
                    break;
            }
        }

        foreach($content["styles"] as $styleId => &$style) {
            if (isset($style["basedOn"])) {
                $parentId = $style["basedOn"];
                if (isset($this->styles[$parentId])) {
                    $this->resolveStyle($style, $parentId, $content["styles"]);
                }
            }

            unset($style["basedOn"]);
        }

        unset($style);

        $reader->close();

        return $content;
    }

    private function parseGlobalStyles(XMLReader $reader): array
    {
        $global = [
            "rPr" => null,
            "pPr" => null
        ];

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::docDefaults->value) {
                break;
            }

            switch ($reader->localName) {
                case Tag::rPr->value:
                    $global["rPr"] = $this->parseRunProperties($reader);
                    break;

                case Tag::pPr->value:
                    $global["pPr"] = $this->parseParagraphProperties($reader);
                    break;
            }
        }

        return $global;
    }

    private function parseStyle(XMLReader $reader): array
    {
        $style = [
            "rPr" => null,
            "pPr" => null,
            "basedOn" => null,
            "name" => null
        ];

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->localName === Tag::style->value) {
                break;
            }

            switch ($reader->localName) {
                case Tag::rPr->value:
                    $style["rPr"] = $this->parseRunProperties($reader);
                    break;

                case Tag::pPr->value:
                    $style["pPr"] = $this->parseParagraphProperties($reader);
                    break;

                case Tag::basedOn->value:
                    $style["basedOn"] = XML::getAttr($reader, Attr::val);
                    break;

                case Tag::name->value:
                    $style["name"] = XML::getAttr($reader, Attr::val);
            }
        }

        return $style;
    }

    private function resolveStyle(array &$style, string $parentId, array $styles): void
    {
        $parentStyle = $styles[$parentId];

        if (isset($parentStyle["basedOn"])) {
            $this->resolveStyle(
                $parentStyle,
                $parentStyle["basedOn"],
                $styles
            );
        }

        if (isset($parentStyle["rPr"])) {
            $style["rPr"] = $style["rPr"]->merge($parentStyle["rPr"]);
        }
        if (isset($parentStyle["pPr"])) {
            $style["pPr"] = $style["pPr"]->merge($parentStyle["pPr"]);
        }

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
                            "rule" => XML::getAttr($reader, Attr::lineRule),
                            "line" => XML::castValue("int", XML::getAttr($reader, Attr::line))
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
