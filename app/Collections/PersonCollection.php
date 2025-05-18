<?php

namespace App\Collections;

use App\Models\Person;
use Illuminate\Support\Collection;

class PersonCollection extends Collection
{
    public function toDisplay(): array
    {
        $output = [];
        $this->each(function (Person $person) use (&$output) {
            $output[] = $person->toDisplay();
        });

        return $output;
    }
}
