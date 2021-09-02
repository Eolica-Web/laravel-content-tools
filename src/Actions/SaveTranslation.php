<?php

declare(strict_types = 1);

namespace Eolica\LaravelContentTools\Actions;

use Eolica\LaravelContentTools\Events\TranslationSaved;
use Eolica\LaravelContentTools\Repository\TranslationRepository;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;

final class SaveTranslation
{
    public function __construct(private TranslationRepository $repository, private EventDispatcher $eventDispatcher)
    {
    }

    public function __invoke(string $locale, string $group, string $key, string $value): void
    {
        $this->repository->save(
            $locale, $group, $key, $value
        );

        $this->eventDispatcher->dispatch(
            new TranslationSaved($locale, $group, $key, $value)
        );
    }
}
