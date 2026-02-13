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
use Symfony\AI\Platform\Bridge\Generic\Completions\ModelClient;
use Symfony\AI\Platform\Bridge\Generic\Embeddings;
use Symfony\AI\Platform\Contract;
use Symfony\AI\Platform\ModelCatalog\FallbackModelCatalog;
use Symfony\AI\Platform\ModelCatalog\ModelCatalogInterface;
use Symfony\AI\Platform\Platform;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Platform factory for amazee.ai's LiteLLM proxy.
 *
 * Unlike GenericPlatformFactory, this uses a custom CompletionsResultConverter
 * that handles LiteLLM returning finish_reason "tool_calls" for structured
 * output responses where the content is in message.content instead of
 * message.tool_calls.
 */
final class PlatformFactory
{
    public static function create(
        string $baseUrl,
        #[\SensitiveParameter] ?string $apiKey = null,
        ?HttpClientInterface $httpClient = null,
        ModelCatalogInterface $modelCatalog = new FallbackModelCatalog(),
        ?Contract $contract = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ): Platform {
        $httpClient = $httpClient instanceof EventSourceHttpClient ? $httpClient : new EventSourceHttpClient($httpClient);

        return new Platform(
            [
                new ModelClient($httpClient, $baseUrl, $apiKey),
                new Embeddings\ModelClient($httpClient, $baseUrl, $apiKey),
            ],
            [
                new CompletionsResultConverter(),
                new Embeddings\ResultConverter(),
            ],
            $modelCatalog,
            $contract,
            $eventDispatcher,
        );
    }
}
