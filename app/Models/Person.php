<?php

namespace App\Models;

use App\Collections\PersonCollection;
use App\Observers\PersonObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Stringable;

#[ObservedBy(PersonObserver::class)]
class Person extends Model implements Stringable
{
    use HasFactory;

    public const DATE_FORMAT = 'Y-m-d H:i:s';

    private const DISPLAY_LIMIT = 20;

    protected $guarded = [
        'id',
    ];

    protected $table = 'people';

    public function newCollection(array $models = []): PersonCollection
    {
        return new PersonCollection($models);
    }

    public function initialString(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->initial ? Str::of($this->initial)->append('.')->toString() : null,
        );
    }

    public function titleString(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->title ? Str::of($this->title)->append('.')->toString() : null,
        );
    }

    public function scopeDisplayImported(Builder $builder): Builder
    {
        return $builder->orderBy('created_at', 'desc')
            ->limit(self::DISPLAY_LIMIT);
    }

    public function toDisplay(): array
    {
        return array_map(
            static fn ($property) => (string) $property,
            $this->only('title', 'first_name', 'initial', 'last_name')
        );
    }

    public function __toString(): string
    {
        $string = sprintf('%s %s %s %s', $this->titleString, $this->first_name, $this->initialString, $this->last_name);

        return Str::of($string)->squish()->toString();
    }
}
