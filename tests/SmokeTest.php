<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

#[CoversNothing]
final class SmokeTest extends WebTestCase
{
    use InteractsWithLiveComponents;

    #[Test]
    public function index(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Welcome to the LLM Chain Demo');
        self::assertSelectorCount(5, '.card');
    }

    #[Test]
    #[DataProvider('provideChats')]
    public function chats(string $path, string $expectedHeadline): void
    {
        $client = static::createClient();
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h4', $expectedHeadline);
        self::assertSelectorCount(1, '#chat-submit');
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideChats(): iterable
    {
        yield 'Blog' => ['/blog', 'Retrieval Augmented Generation based on the Symfony blog'];
        yield 'YouTube' => ['/youtube', 'Chat about a YouTube Video'];
        yield 'Wikipedia' => ['/wikipedia', 'Wikipedia Research'];
    }
}
