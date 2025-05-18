<?php

namespace App\Console\Commands;

use App\Exceptions\InvalidFileTypeException;
use App\Services\PersonImportService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

class ImportPeopleCommand extends Command
{
    protected $signature = 'import:people {file}';

    protected $description = 'Imports people from a CSV file';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return 1;
        }

        try {
            $file = new UploadedFile($file, basename($file), 'csv', null, true);
            PersonImportService::fromCsv($file)->process()->each(function ($person) {
                $this->info(print_r($person->toDisplay(), true));
            });

            return 0;
        } catch (InvalidFileTypeException $e) {
            $this->error($e->getMessage());

            return 1;
        }

    }
}
