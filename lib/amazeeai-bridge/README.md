# amazee.ai Platform Bridge for Symfony AI

This bridge integrates [amazee.ai](https://amazee.ai)'s LiteLLM proxy with
Symfony AI. It wraps the Generic platform with a custom `CompletionsResultConverter`
that handles LiteLLM's `finish_reason: "tool_calls"` quirk for structured output
responses, and a `ModelApiCatalog` that discovers available models from the
`/model/info` endpoint.

## Installation

```bash
composer require symfony/ai-amazeeai-platform
```

## Configuration

Set the required environment variables:

```bash
AMAZEEAI_LLM_API_URL=https://your-litellm-instance.amazee.ai
AMAZEEAI_LLM_KEY=your-api-key
```

## Usage

### Standalone

```php
use Symfony\AI\Platform\Bridge\AmazeeAi\ModelApiCatalog;
use Symfony\AI\Platform\Bridge\AmazeeAi\PlatformFactory;

$catalog = new ModelApiCatalog($httpClient, $baseUrl, $apiKey);
$platform = PlatformFactory::create($baseUrl, $apiKey, modelCatalog: $catalog);
```

### With Symfony AI Bundle

Configure the platform as a `generic` platform in `config/packages/ai.yaml`
and use a compiler pass to swap in the bridge's `PlatformFactory`:

```yaml
ai:
    platform:
        amazeeai:
            base_url: '%env(AMAZEEAI_LLM_API_URL)%'
            api_key: '%env(AMAZEEAI_LLM_KEY)%'
```

### Resources

* [Contributing](https://symfony.com/doc/current/contributing/index.html)
* [Report issues](https://github.com/symfony/ai/issues) and
  [send Pull Requests](https://github.com/symfony/ai/pulls)
  in the [main Symfony AI repository](https://github.com/symfony/ai)
