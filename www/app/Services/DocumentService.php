<?php

namespace App\Services;

use App\Models\Validation;
use App\Parsers\StylesParser;
use App\Validators\Paragraph\AlignmentValidator;
use App\Validators\Paragraph\FontSizeValidator;
use App\Validators\Paragraph\FontValidator;
use App\Validators\Paragraph\SpacingValidator;
use App\Validators\StructureValidator;
use App\Validators\TextValidator;
use Exception;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class DocumentService
{
    private StylesParser $stylesParser;

    public function __construct(StylesParser $stylesParser) {
        $this->stylesParser = $stylesParser;
    }

    public function validate(string $filename): void
    {
        $zip = new ZipArchive();
        if (!$zip->open($filename)) {
            throw new Exception("Не удалось открыть файл .docx");
        }

        $xmls = [
            "document" => $zip->getFromName("word/document.xml"),
            "styles" => $zip->getFromName("word/styles.xml")
        ];

        foreach($xmls as $name => $xml) {
            if ($xml === null) {
                throw new Exception("Файл '$name' не найден в архиве.");
            }
        }

        $stylesContent = $this->stylesParser->parse($xmls["styles"]);

        $errors = [];
        $validators = [
            new TextValidator([
                new FontValidator($stylesContent["global_styles"], $stylesContent["styles"]),
                new FontSizeValidator($stylesContent["global_styles"], $stylesContent["styles"]),
                new SpacingValidator($stylesContent["global_styles"], $stylesContent["styles"]),
                new AlignmentValidator($stylesContent["global_styles"], $stylesContent["styles"])
            ]),
            new StructureValidator()
        ];

        foreach ($validators as $validator)
        {
            $errors = array_merge($errors, $validator->validate($xmls["document"]));
        }

        Validation::create([
            'user_id' => Auth::id(),
            'errors' => array_map(fn($error) => [
                'location' => $error->getLocation(),
                'type' => $error->getType(),
                'message' => $error->getMessage(),
                'context' => $error->getContext()
            ], $errors),
        ]);
    }
}
