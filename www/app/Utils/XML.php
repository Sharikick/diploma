<?php

namespace App\Utils;

use App\Types\Attr;
use Exception;
use XMLReader;

class XML {
    private const NAMESPACE = "http://schemas.openxmlformats.org/wordprocessingml/2006/main";

    public static function getAttr(XMLReader $reader, Attr $attr): ?string
    {
        return $reader->getAttributeNs($attr->value, self::NAMESPACE);
    }

    public static function getAttributes(XMLReader $reader): array
    {
        $attributes = [];

        if ($reader->hasAttributes) {
            while ($reader->moveToNextAttribute()) {
                $attributes[$reader->localName] = $reader->value;
            }
            $reader->moveToElement();
        }

        return $attributes;
    }

    public static function castValue(string $type, mixed $val): mixed
    {
        if ($val === null || $val === "") {
            return null;
        }

        return match ($type) {
            "int" => intval($val),
            "string" => strval($val),
            "float" => floatval($val),
            "bool" => boolval($val),
            default => throw new Exception("Неправильный тип")
        };
    }
}
