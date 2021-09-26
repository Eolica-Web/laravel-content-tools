<?php

declare(strict_types = 1);

namespace Eolica\LaravelContentTools\Repository;

use Brick\VarExporter\VarExporter;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

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
        return "{$this->path}/{$locale}/{$group}.php";
    }
}
