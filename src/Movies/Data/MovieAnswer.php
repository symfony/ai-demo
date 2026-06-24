<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Movies\Data;

/**
 * Structured assistant reply combining a written answer with the movies to render as cards.
 *
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class MovieAnswer
{
    /**
     * @var string The written, conversational answer to the user, formatted as markdown
     */
    public string $answer;

    /**
     * @var MovieSuggestion[] Movies to display as cards beneath the answer; empty when none are relevant
     */
    public array $movies = [];
}
