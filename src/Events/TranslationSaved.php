<?php

declare(strict_types = 1);

namespace Eolica\LaravelContentTools\Events;

final class TranslationSaved
{
    public function __construct(
        private string $locale,
        private string $group,
        private string $key,
        private string $value
    ) {
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }
}
