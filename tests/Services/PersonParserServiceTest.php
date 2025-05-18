<?php

namespace Tests\Services;

use App\Services\PersonParserService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Iterator;
use Tests\TestCase;

class PersonParserServiceTest extends TestCase
{
    use DatabaseMigrations;

    public static function PersonParserService(): Iterator
    {
        Carbon::setTestNow(Carbon::now());

        yield 'Mr John Smith' => [
            'input' => 'Mr John Smith',
            'array' => [
                0 => [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'initial' => null,
                ],
            ],
            'string' => 'Mr. John Smith',
        ];
        yield 'Mr And Mrs Smith' => [
            'input' => 'Mr and Mrs Smith',
            'array' => [
                0 => [
                    'title' => 'Mr',
                    'first_name' => null,
                    'last_name' => 'Smith',
                    'initial' => null,
                ],
                1 => [
                    'title' => 'Mrs',
                    'first_name' => null,
                    'last_name' => 'Smith',
                    'initial' => null,
                ],
            ],
            'string' => 'Mr. and Mrs. Smith',
        ];
        yield 'Mr J. Smith' => [
            'input' => 'Mr J. Smith',
            'array' => [
                0 => [
                    'title' => 'Mr',
                    'first_name' => null,
                    'last_name' => 'Smith',
                    'initial' => 'J',
                ],
            ],
            'string' => 'Mr. J. Smith',
        ];
    }

    /**
     * @dataProvider PersonParserService
     *
     * @test
     */
    public function person_parser_service(string $input, array $array, string $string): void
    {
        $collection = PersonParserService::init($input)->save();
        $testArray = [];

        $collection->each(function ($person, $index) use (&$testArray) {
            $testArray[$index] = $person->only([
                'title',
                'first_name',
                'last_name',
                'initial',
            ]);
        });

        $this->assertEquals($array, $testArray);
    }
}
