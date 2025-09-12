<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Blog;

use App\Blog\FeedLoader;
use App\Blog\Post;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Store\Document\TextDocument;
use Symfony\AI\Store\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Uid\Uuid;

#[CoversClass(FeedLoader::class)]
#[UsesClass(Post::class)]
final class FeedLoaderTest extends TestCase
{
    public function testLoadWithValidFeedUrl()
    {
        $loader = new FeedLoader(new MockHttpClient(MockResponse::fromFile(__DIR__.'/fixtures/symfony-feed.xml')));
        $documents = iterator_to_array($loader->load('https://feeds.feedburner.com/symfony/blog'));

        $this->assertCount(2, $documents);

        // Test first document
        $firstDocument = $documents[0];
        $this->assertInstanceOf(TextDocument::class, $firstDocument);

        $expectedFirstUuid = Uuid::v5(Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8'), 'Save the date, SymfonyDay Montreal 2026!');
        $this->assertEquals($expectedFirstUuid, $firstDocument->id);

        $this->assertStringContainsString('Title: Save the date, SymfonyDay Montreal 2026!', $firstDocument->text);
        $this->assertStringContainsString('From: Paola Suárez on 2025-09-11', $firstDocument->text);
        $this->assertStringContainsString("We're thrilled to announce that SymfonyDay Montreal is happening on June 4, 2026!", $firstDocument->text);
        $this->assertStringContainsString('Mark your calendars, tell your friends', $firstDocument->text);

        $firstMetadata = $firstDocument->metadata->toArray();
        $this->assertSame($expectedFirstUuid->toRfc4122(), $firstMetadata['id']);
        $this->assertSame('Save the date, SymfonyDay Montreal 2026!', $firstMetadata['title']);
        $this->assertSame('https://symfony.com/blog/save-the-date-symfonyday-montreal-2026?utm_source=Symfony%20Blog%20Feed&utm_medium=feed', $firstMetadata['link']);
        $this->assertStringContainsString("We're thrilled to announce that SymfonyDay Montreal is happening on June 4, 2026!", $firstMetadata['description']);
        $this->assertStringContainsString('Mark your calendars, tell your friends', $firstMetadata['content']);
        $this->assertSame('Paola Suárez', $firstMetadata['author']);
        $this->assertSame('2025-09-11', $firstMetadata['date']);

        // Test second document
        $secondDocument = $documents[1];
        $this->assertInstanceOf(TextDocument::class, $secondDocument);

        $expectedSecondUuid = Uuid::v5(Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8'), 'SymfonyCon Amsterdam 2025: Call for IT student volunteers: Volunteer, Learn & Connect!');
        $this->assertEquals($expectedSecondUuid, $secondDocument->id);

        $this->assertStringContainsString('Title: SymfonyCon Amsterdam 2025: Call for IT student volunteers: Volunteer, Learn & Connect!', $secondDocument->text);
        $this->assertStringContainsString('From: Paola Suárez on 2025-09-10', $secondDocument->text);
        $this->assertStringContainsString('🎓SymfonyCon Amsterdam 2025: Call for IT Student Volunteers!', $secondDocument->text);

        $secondMetadata = $secondDocument->metadata->toArray();
        $this->assertSame($expectedSecondUuid->toRfc4122(), $secondMetadata['id']);
        $this->assertSame('SymfonyCon Amsterdam 2025: Call for IT student volunteers: Volunteer, Learn & Connect!', $secondMetadata['title']);
        $this->assertSame('https://symfony.com/blog/symfonycon-amsterdam-2025-call-for-it-student-volunteers-volunteer-learn-and-connect?utm_source=Symfony%20Blog%20Feed&utm_medium=feed', $secondMetadata['link']);
        $this->assertStringContainsString('🎓SymfonyCon Amsterdam 2025: Call for IT Student Volunteers!', $secondMetadata['description']);
        $this->assertStringContainsString('🎓SymfonyCon Amsterdam 2025: Call for IT Student Volunteers!', $secondMetadata['content']);
        $this->assertSame('Paola Suárez', $secondMetadata['author']);
        $this->assertSame('2025-09-10', $secondMetadata['date']);
    }

    public function testLoadWithNullSource()
    {
        $loader = new FeedLoader(new MockHttpClient([]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('FeedLoader requires a RSS feed URL as source, null given.');

        iterator_to_array($loader->load(null));
    }

    public function testLoadWithEmptyFeed()
    {
        $emptyFeedXml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
    <channel>
        <title>Empty Feed</title>
        <link>https://example.com/</link>
        <description>An empty RSS feed</description>
    </channel>
</rss>
XML;

        $loader = new FeedLoader(new MockHttpClient(new MockResponse($emptyFeedXml)));
        $documents = iterator_to_array($loader->load('https://example.com/feed.xml'));

        $this->assertCount(0, $documents);
    }

    public function testLoadWithHttpError()
    {
        $loader = new FeedLoader(new MockHttpClient(new MockResponse('', ['http_code' => 404])));

        $this->expectException(\Symfony\Contracts\HttpClient\Exception\ClientException::class);

        iterator_to_array($loader->load('https://example.com/non-existent-feed.xml'));
    }

    public function testLoadWithMalformedXml()
    {
        $malformedXml = '<?xml version="1.0" encoding="UTF-8" ?><rss><channel><title>Test</title>';

        $loader = new FeedLoader(new MockHttpClient(new MockResponse($malformedXml)));

        $this->expectException(\Exception::class);

        iterator_to_array($loader->load('https://example.com/malformed-feed.xml'));
    }

    public function testLoadReturnsIterableOfTextDocuments()
    {
        $loader = new FeedLoader(new MockHttpClient(MockResponse::fromFile(__DIR__.'/fixtures/symfony-feed.xml')));
        $result = $loader->load('https://feeds.feedburner.com/symfony/blog');

        $this->assertIsIterable($result);

        foreach ($result as $document) {
            $this->assertInstanceOf(TextDocument::class, $document);
            $this->assertInstanceOf(Uuid::class, $document->id);
            $this->assertIsString($document->text);
            $this->assertNotEmpty($document->text);
            $this->assertIsArray($document->metadata->toArray());
        }
    }

    public function testLoadGeneratesConsistentUuids()
    {
        $loader = new FeedLoader(new MockHttpClient(MockResponse::fromFile(__DIR__.'/fixtures/symfony-feed.xml')));
        $documents1 = iterator_to_array($loader->load('https://feeds.feedburner.com/symfony/blog'));

        // Load same feed again
        $loader2 = new FeedLoader(new MockHttpClient(MockResponse::fromFile(__DIR__.'/fixtures/symfony-feed.xml')));
        $documents2 = iterator_to_array($loader2->load('https://feeds.feedburner.com/symfony/blog'));

        $this->assertCount(2, $documents1);
        $this->assertCount(2, $documents2);

        // UUIDs should be identical for same content
        $this->assertEquals($documents1[0]->id, $documents2[0]->id);
        $this->assertEquals($documents1[1]->id, $documents2[1]->id);
    }
}
