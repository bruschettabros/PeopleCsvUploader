<?php

namespace Tests\Services;

use App\Exceptions\InvalidFileTypeException;
use App\Models\Person;
use App\Services\PersonImportService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PersonImportServiceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function invalid_csv(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 1024, 'image/jpeg');

        $this->expectException(InvalidFileTypeException::class);
        PersonImportService::fromCsv($file);
    }

    /**
     * @test
     */
    public function valid_content(): void
    {
        $this->assertDatabaseEmpty((new Person)->getTable());

        $file = UploadedFile::fake()->createWithContent('test.csv', 'Mr John Smith' . PHP_EOL . 'Mr John Doe');
        $instance = PersonImportService::fromCsv($file)->process();

        $this->assertDatabaseCount((new Person)->getTable(), 2);
        $this->assertDatabaseHas((new Person)->getTable(), [
            'title' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'initial' => null,
        ]);
        $this->assertDatabaseHas((new Person)->getTable(), [
            'title' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'initial' => null,
        ]);
    }
}
