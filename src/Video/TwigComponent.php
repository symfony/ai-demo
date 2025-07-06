<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Video;

use Symfony\AI\Platform\Bridge\OpenAI\GPT;
use Symfony\AI\Platform\Message\Content\Image;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('video')]
final class TwigComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public string $caption = 'Please define an instruction and hit submit.';

    public function __construct(
        private readonly PlatformInterface $platform,
    ) {
    }

    #[LiveAction]
    public function submit(#[LiveArg] string $instruction, #[LiveArg] string $image): void
    {
        $messageBag = new MessageBag(
            Message::forSystem(<<<PROMPT
                You are a video captioning assistant. You are provided with a video frame and an instruction.
                You must generate a caption or answer based on the provided video frame and the user's instruction.
                You are not in a conversation with the user and there will be no follow-up questions or messages.
                PROMPT),
            Message::ofUser($instruction, Image::fromDataUrl($image))
        );

        $response = $this->platform->request(new GPT(GPT::GPT_4O_MINI), $messageBag, [
            'max_tokens' => 100,
        ]);

        $this->caption = $response->asText();
    }
}
