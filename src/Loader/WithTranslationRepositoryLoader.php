<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\Loader;

use Eolica\LaravelContentTools\Repository\TranslationRepository;
use Illuminate\Contracts\Translation\Loader;

final class WithTranslationRepositoryLoader implements Loader
{
    public function __construct(private TranslationRepository $repository, private Loader $loader)
    {
    }

    public function load($locale, $group, $namespace = null): array
    {
        $defaultTranslations = $this->loader->load($locale, $group, $namespace);

        if ($namespace !== null && $namespace !== '*') {
            return $defaultTranslations;
        }

        return array_replace_recursive($defaultTranslations, $this->repository->load($locale, $group));
    }

    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->loader->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->loader->namespaces();
    }
}
