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

namespace Symfony\AI\Platform\Bridge\AmazeeAi\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\AI\Platform\Bridge\AmazeeAi\PlatformFactory;
use Symfony\AI\Platform\Platform;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class PlatformFactoryTest extends TestCase
{
    public function testCreateWithDefaults(): void
    {
        $platform = PlatformFactory::create(
            'https://litellm.example.com',
            'test-api-key',
        );

        $this->assertInstanceOf(Platform::class, $platform);
    }

    public function testCreateWithCustomHttpClient(): void
    {
        $httpClient = new MockHttpClient();

        $platform = PlatformFactory::create(
            'https://litellm.example.com',
            'test-api-key',
            $httpClient,
        );

        $this->assertInstanceOf(Platform::class, $platform);
    }

    public function testCreateWithEventSourceHttpClient(): void
    {
        $httpClient = new EventSourceHttpClient(new MockHttpClient());

        $platform = PlatformFactory::create(
            'https://litellm.example.com',
            'test-api-key',
            $httpClient,
        );

        $this->assertInstanceOf(Platform::class, $platform);
    }

}
