<?php

namespace App\Observers;

use Illuminate\Support\Str;

class PersonObserver
{
    public function creating($model): void
    {
        $this->prepNames($model);
    }

    public function updating($model): void
    {
        $this->prepNames($model);
    }

    private function prepNames($model): void
    {
        collect(['title', 'first_name', 'last_name', 'initial'])->each(function ($name) use ($model) {
            $string = Str::of($model->$name);
            $model->$name = $string->isEmpty() ? null : $string->ucfirst();
        });

    }
}
