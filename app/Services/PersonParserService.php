<?php

namespace App\Services;

use App\Collections\PersonCollection;
use App\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PersonParserService
{
    private const PERSON_GLUE = ' and ';
    private const MORE_PERSON_GLUE = ' & ';
    private const SEPARATOR = ' ';

    // This should be refactored to a config, Probably database
    private const ACCEPTABLE_TITLES = [
        'Mr',
        'Mrs',
        'Ms',
        'Dr',
        'Prof',
        'Mister',
    ];

    private const INITIAL_SUFFIX = '.';

    private bool $hasSecondPerson = false;
    private array $person = [];
    private array $secondPerson = [];

    private PersonCollection $personCollection;

    public function __construct(string $person)
    {
        $this->personCollection = new PersonCollection;
        $this->splitNames($person);

        if ($this->hasSecondPerson) {
            $this->extractTitle($this->secondPerson)
                ->extractName($this->secondPerson, 'last_name')
                ->extractName($this->secondPerson);
        }
        $this->extractTitle($this->person)
            ->extractInitial()
            ->extractName($this->person, 'last_name', $this->secondPerson['processed']['last_name'] ?? null)
            ->extractName($this->person, 'first_name', $this->secondPerson['processed']['first_name'] ?? null);
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

    private function deGlue(string $person): Collection
    {
        Str::of($person);
        $names = Str::of($person)->explode(self::PERSON_GLUE);

        if ($names->count() === 1) {
            $names = Str::of($person)->explode(self::MORE_PERSON_GLUE);
        }

        return $names;

    }

    private function splitNames(string $person): void
    {
        $names = $this->deGlue($person);

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

    private function extractTitle(array &$person): self
    {
        $potentialTitle = Str::of(($person['raw'][0]) ?? '');

        if ($potentialTitle->ucfirst()->contains(self::ACCEPTABLE_TITLES)) {
            $person['processed']['title'] = $person['raw'][0] ?? null;
            unset($person['raw'][0]);
        }

        return $this;
    }

    private function extractName(array &$person, string $field = 'first_name', ?string $fallbackName = null)
    {
        $potentialName = end($person['raw']);

        if ($potentialName !== false) {
            $person['processed'][$field] = $potentialName;
            array_pop($person['raw']);
        } else {
            $person['processed'][$field] = $fallbackName;
        }

        return $this;
    }

    private function extractInitial(): self
    {
        collect($this->person['raw'])->each(function ($part, $key) {
            $string = Str::of($part)->replace(self::INITIAL_SUFFIX, '');

            if ($string->length() === 1) {
                $this->person['processed']['initial'] = (string) $string;
                unset($this->person['raw'][$key]);
            }
        });

        return $this;
    }
}
