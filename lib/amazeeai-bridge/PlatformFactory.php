<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\AI\Platform\Bridge\AmazeeAi;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\AI\Platform\Bridge\Generic\Embeddings;
use Symfony\AI\Platform\Contract;
use Symfony\AI\Platform\ModelCatalog\FallbackModelCatalog;
use Symfony\AI\Platform\ModelCatalog\ModelCatalogInterface;
use Symfony\AI\Platform\Platform;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\AI\Platform\Bridge\Generic\Completions\ModelClient;

/**
 * Platform factory for amazee.ai's LiteLLM proxy.
 *
 * Uses a custom CompletionsResultConverter that handles LiteLLM returning
 * finish_reason "tool_calls" for structured output responses where the
 * content is in message.content instead of message.tool_calls.
 */
class PlatformFactory
{
    public static function create(
        string $baseUrl,
        #[\SensitiveParameter] ?string $apiKey = null,
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
            $modelClients[] = new ModelClient($httpClient, $baseUrl, $apiKey, $completionsPath);
            $resultConverters[] = new CompletionsResultConverter();
        }
        if ($supportsEmbeddings) {
            $modelClients[] = new Embeddings\ModelClient($httpClient, $baseUrl, $apiKey, $embeddingsPath);
            $resultConverters[] = new Embeddings\ResultConverter();
        }

        return new Platform($modelClients, $resultConverters, $modelCatalog, $contract, $eventDispatcher);
    }
}
