<?php

namespace App\Validators;

use App\Types\Tag;
use XMLReader;

class StructureValidator
{
    private array $errors = [];

    public function validate(string $xmlContent): array
    {
        $isToc = $this->checkTOC($xmlContent);

        return $this->errors;
    }

    private function checkTOC(string $xmlContent): bool
    {
        $toc = false;

        $reader = new XMLReader();
        $reader->XML($xmlContent);

        while ($reader->read())
        {
            if ($reader->nodeType === XMLReader::END_ELEMENT) continue;

            switch ($reader->localName)
            {
                case Tag::sdt->value:
                    break;
            }
        }

        $reader->close();

        return $toc;
    }
}
