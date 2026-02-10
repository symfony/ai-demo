# CHANGELOG

## 0.1.0

* Initial release
* `PlatformFactory` wrapping Generic platform with LiteLLM-aware result converter
* `CompletionsResultConverter` handling `finish_reason: "tool_calls"` with content fallback
* `ModelApiCatalog` with lazy loading from `/model/info` endpoint
