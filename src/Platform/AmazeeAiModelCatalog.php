<?php

declare(strict_types=1);

namespace App\Platform;

use Symfony\AI\Platform\Bridge\Generic\CompletionsModel;
use Symfony\AI\Platform\Bridge\Generic\EmbeddingsModel;
use Symfony\AI\Platform\Capability;
use Symfony\AI\Platform\Model;
use Symfony\AI\Platform\ModelCatalog\ModelCatalogInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Model catalog that discovers available models from the amazee.ai LiteLLM /model/info endpoint.
 *
 * Maps each model to CompletionsModel or EmbeddingsModel based on the mode field,
 * so the Generic platform's ModelClients can route requests correctly.
 */
final class AmazeeAiModelCatalog implements ModelCatalogInterface
{
    /** @var array<string, array{class: class-string<Model>, capabilities: list<Capability>}>|null */
    private ?array $models = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $baseUrl,
        private readonly ?string $apiKey = null,
    ) {
    }

    public function getModel(string $modelName): Model
    {
        $models = $this->loadModels();

        $parsed = $this->parseModelName($modelName);
        $catalogKey = $parsed['catalogKey'];
        $options = $parsed['options'];
        $name = $parsed['name'];

        if (!isset($models[$catalogKey])) {
            throw new \InvalidArgumentException(\sprintf('Model "%s" not found in amazee.ai model catalog.', $modelName));
        }

        $config = $models[$catalogKey];

        return new $config['class']($name, $config['capabilities'], $options);
    }

    /**
     * @return array<string, array{class: class-string<Model>, capabilities: list<Capability>}>
     */
    public function getModels(): array
    {
        return $this->loadModels();
    }

    /**
     * @return array<string, array{class: class-string<Model>, capabilities: list<Capability>}>
     */
    private function loadModels(): array
    {
        if (null !== $this->models) {
            return $this->models;
        }

        $this->models = [];

        $response = $this->httpClient->request('GET', $this->baseUrl.'/model/info', [
            'headers' => array_filter([
                'Authorization' => $this->apiKey ? 'Bearer '.$this->apiKey : null,
            ]),
        ]);

        $data = $response->toArray();

        foreach ($data['data'] ?? [] as $modelInfo) {
            $name = $modelInfo['model_name'] ?? null;
            if (null === $name) {
                continue;
            }

            $info = $modelInfo['model_info'] ?? [];
            $mode = $info['mode'] ?? null;

            if ('embedding' === $mode) {
                $this->models[$name] = [
                    'class' => EmbeddingsModel::class,
                    'capabilities' => $this->buildEmbeddingCapabilities($info),
                ];
            } else {
                $this->models[$name] = [
                    'class' => CompletionsModel::class,
                    'capabilities' => $this->buildCompletionsCapabilities($info),
                ];
            }
        }

        return $this->models;
    }

    /**
     * @param array<string, mixed> $info
     *
     * @return list<Capability>
     */
    private function buildEmbeddingCapabilities(array $info): array
    {
        $capabilities = [Capability::EMBEDDINGS, Capability::INPUT_TEXT];

        if ($info['supports_multiple_inputs'] ?? true) {
            $capabilities[] = Capability::INPUT_MULTIPLE;
        }

        return $capabilities;
    }

    /**
     * @param array<string, mixed> $info
     *
     * @return list<Capability>
     */
    private function buildCompletionsCapabilities(array $info): array
    {
        $capabilities = [Capability::INPUT_MESSAGES, Capability::OUTPUT_TEXT, Capability::OUTPUT_STREAMING];

        if ($info['supports_image_input'] ?? false) {
            $capabilities[] = Capability::INPUT_IMAGE;
        }
        if ($info['supports_audio_input'] ?? false) {
            $capabilities[] = Capability::INPUT_AUDIO;
        }
        if ($info['supports_tool_calling'] ?? $info['supports_function_calling'] ?? false) {
            $capabilities[] = Capability::TOOL_CALLING;
        }
        if ($info['supports_response_schema'] ?? false) {
            $capabilities[] = Capability::OUTPUT_STRUCTURED;
        }

        return $capabilities;
    }

    /**
     * @return array{name: non-empty-string, catalogKey: non-empty-string, options: array<string, mixed>}
     */
    private function parseModelName(string $modelName): array
    {
        if ('' === $modelName) {
            throw new \InvalidArgumentException('Model name cannot be empty.');
        }

        /** @var array<string, mixed> $options */
        $options = [];
        $actualModelName = $modelName;

        if (str_contains($modelName, '?')) {
            [$actualModelName, $queryString] = explode('?', $modelName, 2);
            if ('' === $actualModelName) {
                throw new \InvalidArgumentException('Model name cannot be empty.');
            }
            $parsed = [];
            parse_str($queryString, $parsed);
            $options = $this->convertScalarStrings(array_combine(
                array_map('strval', array_keys($parsed)),
                array_values($parsed),
            ));
        }

        $catalogKey = $actualModelName;
        $models = $this->loadModels();
        if (!isset($models[$actualModelName]) && str_contains($actualModelName, ':')) {
            $baseModelName = explode(':', $actualModelName, 2)[0];
            if ('' !== $baseModelName && isset($models[$baseModelName])) {
                $catalogKey = $baseModelName;
            }
        }

        return [
            'name' => $actualModelName,
            'catalogKey' => $catalogKey,
            'options' => $options,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function convertScalarStrings(array $data): array
    {
        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $data[$key] = $this->convertScalarStrings($value);
            } elseif ('true' === $value) {
                $data[$key] = true;
            } elseif ('false' === $value) {
                $data[$key] = false;
            } elseif (is_numeric($value) && \is_string($value)) {
                $data[$key] = str_contains($value, '.') ? (float) $value : (int) $value;
            }
        }

        return $data;
    }
}
