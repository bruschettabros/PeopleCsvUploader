<?php

namespace App\Services;

use App\Collections\PersonCollection;
use App\Models\Person;
use Illuminate\Support\Str;

class PersonParserService
{
    private const PERSON_GLUE = ' and ';
    private const SEPARATOR = ' ';

    // This should be refactored to a config, Probably database
    private const ACCEPTABLE_TITLES = [
        'Mr',
        'Mrs',
        'Ms',
        'Dr',
        'Prof',
    ];

    private const INITIAL_SUFFIX = '.';

    private bool $hasSecondPerson = false;
    private array $person = [];
    private array $secondPerson = [];

    private PersonCollection $personCollection;

    public function __construct(string $person)
    {
        $this->personCollection = new PersonCollection;
        $this->extractNames($person);
        $this->extractTitle();
        $this->extractLastName();
        $this->extractInitial();
        $this->extractFirstName();

    }

    public static function init(string $person): self
    {
        return new self($person);
    }

    public function save(): PersonCollection
    {

        $toSave = collect([$this->person]);
        if ($this->hasSecondPerson) {
            $toSave = $toSave->add($this->secondPerson);
        }

        $toSave->each(function ($person) {
            if (empty($person['processed']['last_name'] ?? null)) {
                return;
            }

            $model = new Person($person['processed']);
            $model->save();
            $this->personCollection->add($model);
        });

        return $this->personCollection;

    }

    private function extractNames(string $person): void
    {
        $names = Str::of($person)->explode(self::PERSON_GLUE);
        if ($names->count() > 1) {
            $this->hasSecondPerson = true;
            $this->secondPerson['raw'] = $this->explodePerson($names->pop());
        }

        $this->person['raw'] = $this->explodePerson($names->pop());
    }

    private function explodePerson(string $person): array
    {
        return Str::of($person)->explode(self::SEPARATOR)->toArray();
    }

    private function extractTitle(): void
    {
        $potentialTitle = Str::of($this->person['raw'][0]);
        if ($potentialTitle->contains(self::ACCEPTABLE_TITLES)) {
            $this->person['processed']['title'] = $this->person['raw'][0] ?? null;
        }

        if ($this->hasSecondPerson) {
            $potentialTitle = Str::of($this->secondPerson['raw'][0]);
            if ($potentialTitle->contains(self::ACCEPTABLE_TITLES)) {
                $this->secondPerson['processed']['title'] = $this->secondPerson['raw'][0] ?? null;
            }
        }
    }

    private function extractLastName(): void
    {
        if ($this->hasSecondPerson) {
            $this->person['processed']['last_name'] = $this->secondPerson['processed']['last_name'] = end($this->secondPerson['raw']);
        } else {
            $this->person['processed']['last_name'] = end($this->person['raw']);
        }
    }

    private function extractFirstName(): void
    {
        $offset = $this->hasTitle() ? 1 : 0;
        $name = Str::of(($this->person['raw'][$offset]) ?? null)->replace(self::INITIAL_SUFFIX, '');
        if ((string) $name === ($this->person['processed']['initial'] ?? null)) {
            $name = null;
        }

        $this->person['processed']['first_name'] = (string) $name;
    }

    private function hasTitle(): bool
    {
        return isset($this->person['processed']['title']);
    }

    private function extractInitial(): void
    {
        collect($this->person['raw'])->each(function ($part) {
            $string = Str::of($part)->replace(self::INITIAL_SUFFIX, '');

            if ($string->length() === 1) {
                $this->person['processed']['initial'] = (string) $string;
            }
        });
    }
}
