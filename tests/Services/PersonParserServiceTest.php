<?php

namespace Tests\Services;

use App\Services\PersonParserService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
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
        ];
        yield 'Dr & Mrs Joe Bloggs' => [
            'input' => 'Dr & Mrs Joe Bloggs',
            'array' => [
                0 => [
                    'title' => 'Dr',
                    'first_name' => 'Joe',
                    'last_name' => 'Bloggs',
                    'initial' => null,
                ],
                1 => [
                    'title' => 'Mrs',
                    'first_name' => 'Joe',
                    'last_name' => 'Bloggs',
                    'initial' => null,
                ],
            ],
        ];
        yield 'Mr Tom Staff and Mr John Doe' => [
            'input' => 'Mr Tom Staff and Mr John Doe',
            'array' => [
                0 => [
                    'title' => 'Mr',
                    'first_name' => 'Tom',
                    'last_name' => 'Staff',
                    'initial' => null,
                ],
                1 => [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'initial' => null,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    #[DataProvider('PersonParserService')]
    public function person_parser_serv(string $input, array $array): void
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
