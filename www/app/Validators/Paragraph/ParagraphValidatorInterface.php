<?php

namespace App\Validators\Paragraph;

use App\Models\Docx\Paragraph;

interface ParagraphValidatorInterface
{
    function validate(Paragraph $paragraph): array;
}
