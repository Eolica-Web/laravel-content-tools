<?php

declare(strict_types = 1);

namespace Eolica\LaravelContentTools\Repository;

interface TranslationRepository
{
    public function save(string $locale, string $group, string $key, string $value): void;

    public function load(string $locale, string $group): array;
}
