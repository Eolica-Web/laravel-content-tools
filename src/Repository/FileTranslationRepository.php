<?php

declare(strict_types = 1);

namespace Eolica\LaravelContentTools\Repository;

use Brick\VarExporter\VarExporter;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FileTranslationRepository implements TranslationRepository
{
    public function __construct(private Filesystem $files, private string $path)
    {
    }

    public function save(string $locale, string $group, string $key, string $value): void
    {
        $translations = $this->load($locale, $group);

        Arr::set($translations, $key, $value);

        ksort($translations);

        $groupPath = $this->getGroupPath($locale, $group);

        if (!$this->files->exists($directory = dirname($groupPath))){
            $this->files->makeDirectory($directory, recursive: true);
        }

        $this->files->put($groupPath, "<?php\n\nreturn " . VarExporter::export($translations) . ';' . PHP_EOL);
    }

    public function load(string $locale, string $group): array
    {
        try {
            return $this->files->getRequire($this->getGroupPath($locale, $group));
        } catch (FileNotFoundException) {
            return [];
        }
    }

    private function getGroupPath(string $locale, string $group): string
    {
        $basePath = $this->getGroupBasePath($locale, $group);

        if (Str::contains($group, '/')){
            $group = explode('/', $group)[1];
        }

        return $basePath . DIRECTORY_SEPARATOR . $group . '.php';
    }

    private function getGroupBasePath(string $locale, string $group): string
    {
        if (Str::contains($group, '/')){
            [$namespace,] = explode('/', $group);

            return $this->path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $locale;
        }

        return $this->path . DIRECTORY_SEPARATOR . $locale;
    }
}
