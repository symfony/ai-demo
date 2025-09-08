<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Blog;

use Symfony\AI\Store\Document\LoaderInterface;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\TextDocument;
use Symfony\AI\Store\Exception\InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FeedLoader implements LoaderInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param ?string              $source  RSS feed URL
     * @param array<string, mixed> $options
     *
     * @return iterable<TextDocument>
     */
    public function load(?string $source, array $options = []): iterable
    {
        if (null === $source) {
            throw new InvalidArgumentException('FeedLoader requires a RSS feed URL as source, null given.');
        }
        $result = $this->httpClient->request('GET', $source);

        $posts = [];
        $crawler = new Crawler($result->getContent());
        $crawler->filter('item')->each(function (Crawler $node) use (&$posts) {
            $title = $node->filter('title')->text();
            $posts[] = new Post(
                Uuid::v5(Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8'), $title),
                $title,
                $node->filter('link')->text(),
                $node->filter('description')->text(),
                (new Crawler($node->filter('content\:encoded')->text()))->text(),
                $node->filter('dc\:creator')->text(),
                new \DateTimeImmutable($node->filter('pubDate')->text()),
            );
        });

        foreach ($posts as $post) {
            yield new TextDocument($post->id, $post->toString(), new Metadata($post->toArray()));
        }
    }
}
