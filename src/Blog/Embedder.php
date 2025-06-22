<?php

declare(strict_types=1);

namespace App\Blog;

use PhpLlm\LlmChain\Store\Document\Metadata;
use PhpLlm\LlmChain\Store\Document\TextDocument;
use PhpLlm\LlmChain\Store\Embedder as LlmChainEmbedder;

final readonly class Embedder
{
    public function __construct(
        private FeedLoader $loader,
        private LlmChainEmbedder $embedder,
    ) {
    }

    public function embedBlog(): void
    {
        $documents = [];
        foreach ($this->loader->load() as $post) {
            $documents[] = new TextDocument($post->id, $post->toString(), new Metadata($post->toArray()));
        }

        $this->embedder->embed($documents);
    }
}
