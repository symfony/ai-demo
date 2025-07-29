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
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(FeedLoader::class)]
#[UsesClass(Post::class)]
final class LoaderTest extends TestCase
{
    public function testLoad()
    {
        $response = MockResponse::fromFile(__DIR__.'/fixtures/blog.rss');
        $client = new MockHttpClient($response);

        $loader = new FeedLoader($client);
        $posts = $loader->load();

        $this->assertCount(10, $posts);

        $this->assertSame('A Week of Symfony #936 (2-8 December 2024)', $posts[0]->title);
        $this->assertSame('https://symfony.com/blog/a-week-of-symfony-936-2-8-december-2024?utm_source=Symfony%20Blog%20Feed&utm_medium=feed', $posts[0]->link);
        $this->assertStringContainsString('This week, Symfony celebrated the SymfonyCon 2024 Vienna conference with great success.', $posts[0]->description);
        $this->assertStringContainsString('Select a track for a guided path through 100+ video tutorial courses about Symfony', $posts[0]->content);
        $this->assertSame('Javier Eguiluz', $posts[0]->author);
        $this->assertEquals(new \DateTimeImmutable('8.12.2024 09:39:00 +0100'), $posts[0]->date);

        $this->assertSame('A Week of Symfony #935 (25 November - 1 December 2024)', $posts[1]->title);
        $this->assertSame('Symfony 7.2 curated new features', $posts[2]->title);
        $this->assertSame('Symfony 7.2.0 released', $posts[3]->title);
        $this->assertSame('Symfony 5.4.49 released', $posts[4]->title);
        $this->assertSame('SymfonyCon Vienna 2024: See you next week!', $posts[5]->title);
        $this->assertSame('New in Symfony 7.2: Misc. Improvements (Part 2)', $posts[6]->title);
        $this->assertSame('Symfony 7.1.9 released', $posts[7]->title);
        $this->assertSame('Symfony 6.4.16 released', $posts[8]->title);
        $this->assertSame('Symfony 5.4.48 released', $posts[9]->title);
    }
}
