<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\Http\Controllers;

use Eolica\LaravelContentTools\Actions\SaveTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class TranslationsPostController
{
    public function __construct(private SaveTranslation $saveTranslation)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $translations   = $request->post('translations');
        $locale         = $request->post('locale');

        foreach ($translations as $translation => $value) {
            [$group, $key] = explode('.', $translation, 2);

            $this->saveTranslation->__invoke($locale, $group, $key, $value ?? '');
        }

        return new JsonResponse([], Response::HTTP_OK);
    }
}
