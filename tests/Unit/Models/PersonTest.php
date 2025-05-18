<?php

namespace Models;

use App\Models\Person;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * @group people-data
 */
class PersonTest extends TestCase
{
    use DatabaseMigrations;

    public static function personProvider()
    {
        yield 'all Data set' => [
            'person' => fn () => Person::factory()->create([
                'title' => 'mr',
                'initial' => 'j',
                'first_name' => 'john',
                'last_name' => 'smith',
            ]),
            'string' => 'Mr. John J. Smith',
            'title' => 'Mr.',
            'initial' => 'J.',
        ];
        yield 'Missing initial' => [
            'person' => fn () => Person::factory()->create([
                'title' => 'mr',
                'initial' => null,
                'first_name' => 'john',
                'last_name' => 'smith',
            ]),
            'string' => 'Mr. John Smith',
            'title' => 'Mr.',
            'initial' => null,
        ];
    }

    /**
     * @dataProvider personProvider
     *
     * @test
     */
    public function stringable(callable $person, string $string, ?string $title, ?string $initial): void
    {
        $person = $person();
        $this->assertEquals($initial, $person->initialString);
        $this->assertEquals($title, $person->titleString);
        $this->assertEquals($string, $person->__toString());
    }
}
