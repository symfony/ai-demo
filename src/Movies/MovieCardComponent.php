<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Movies;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
#[AsTwigComponent('movie_card')]
final class MovieCardComponent
{
    public Movie $movie;
}
