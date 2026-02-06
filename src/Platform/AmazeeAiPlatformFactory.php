<?php

declare(strict_types=1);

namespace App\Platform;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\AI\Platform\Bridge\Generic\CompletionsModel;
use Symfony\AI\Platform\Bridge\Generic\Embeddings;
use Symfony\AI\Platform\Contract;
use Symfony\AI\Platform\ModelCatalog\FallbackModelCatalog;
use Symfony\AI\Platform\ModelCatalog\ModelCatalogInterface;
use Symfony\AI\Platform\Platform;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Platform factory that uses FixedCompletionsResultConverter to handle
 * LiteLLM returning finish_reason "tool_calls" for structured output.
 */
class AmazeeAiPlatformFactory
{
    public static function create(
        string $baseUrl,
        ?string $apiKey = null,
        ?HttpClientInterface $httpClient = null,
        ModelCatalogInterface $modelCatalog = new FallbackModelCatalog(),
        ?Contract $contract = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        bool $supportsCompletions = true,
        bool $supportsEmbeddings = true,
        string $completionsPath = '/v1/chat/completions',
        string $embeddingsPath = '/v1/embeddings',
    ): Platform {
        $httpClient = $httpClient instanceof EventSourceHttpClient ? $httpClient : new EventSourceHttpClient($httpClient);

        $modelClients = [];
        $resultConverters = [];
        if ($supportsCompletions) {
            $modelClients[] = new \Symfony\AI\Platform\Bridge\Generic\Completions\ModelClient($httpClient, $baseUrl, $apiKey, $completionsPath);
            $resultConverters[] = new FixedCompletionsResultConverter();
        }
        if ($supportsEmbeddings) {
            $modelClients[] = new Embeddings\ModelClient($httpClient, $baseUrl, $apiKey, $embeddingsPath);
            $resultConverters[] = new Embeddings\ResultConverter();
        }

        return new Platform($modelClients, $resultConverters, $modelCatalog, $contract, $eventDispatcher);
    }
}
