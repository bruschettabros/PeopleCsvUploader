<?php

namespace App\Services;

use App\Collections\PersonCollection;
use App\Exceptions\InvalidFileTypeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PersonImportService
{
    public function __construct(private readonly array $data) {}

    public static function fromCsv(UploadedFile $file): self
    {
        if (! Str::of($file->getMimeType())->contains(['csv', 'text'])) {
            throw new InvalidFileTypeException('Invalid file type. Only CSV files are allowed.');
        }

        return new self(Str::of($file->getContent())->explode(PHP_EOL)->toArray());
    }

    public function process(): PersonCollection
    {
        $collection = new PersonCollection;
        collect($this->data)->each(function ($person) use ($collection) {
            $results = PersonParserService::init($person)->save();
            $results->each(function ($person) use ($collection) {
                $collection->add($person);
            });
        });

        return $collection;
    }
}
