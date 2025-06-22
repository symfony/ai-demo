<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Wikipedia;

use Symfony\AI\Platform\Message\MessageInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('wikipedia')]
final class TwigComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly Chat $wikipedia,
    ) {
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages(): array
    {
        return $this->wikipedia->loadMessages()->withoutSystemMessage()->getMessages();
    }

    #[LiveAction]
    public function submit(#[LiveArg] string $message): void
    {
        $this->wikipedia->submitMessage($message);
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->wikipedia->reset();
    }
}
