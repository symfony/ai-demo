<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\YouTube;

use App\YouTube\TranscriptFetcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(TranscriptFetcher::class), ]
final class TranscriptFetcherTest extends TestCase
{
    public function testFetchTranscript(): void
    {
        $videoResponse = MockResponse::fromFile(__DIR__.'/fixtures/video.html');
        $transcriptResponse = MockResponse::fromFile(__DIR__.'/fixtures/transcript.xml');
        $mockClient = new MockHttpClient([$videoResponse, $transcriptResponse]);

        $fetcher = new TranscriptFetcher($mockClient);
        $transcript = $fetcher->fetchTranscript('6uXW-ulpj0s');

        self::assertStringContainsString('symphony is a PHP framework', $transcript);
    }
}
