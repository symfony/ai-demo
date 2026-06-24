<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Stream;

use Symfony\AI\Platform\Message\MessageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\EventStreamResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerEvent;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('stream')]
final class TwigComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $message = null;
    public bool $stream = false;

    public function __construct(
        private readonly Chat $chat,
    ) {
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages(): array
    {
        return $this->chat->loadMessages()->withoutSystemMessage()->withoutToolMessages()->getMessages();
    }

    #[LiveAction]
    public function submit(): void
    {
        if (!$this->message) {
            return;
        }

        $this->chat->submitMessage($this->message);
        $this->message = null;
        $this->stream = true;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->chat->reset();
    }

    public function streamContent(Request $request): EventStreamResponse
    {
        // The chat is kept in a session-scoped cache, so make sure the session id is
        // available; the streamed body itself never touches the session, which lets the
        // framework close (and unlock) it before the response is sent.
        $request->getSession()->start();

        $messages = $this->chat->loadMessages();

        return new EventStreamResponse(function () use ($messages) {
            $response = $this->chat->getAssistantResponse($messages);

            foreach ($response as $partialMessage) {
                yield new ServerEvent(explode("\n", $this->renderBlockView('_stream.html.twig', 'update', ['message' => $partialMessage])));
            }

            yield new ServerEvent(explode("\n", $this->renderBlockView('_stream.html.twig', 'end', ['message' => $response->getReturn()])));
        });
    }
}
