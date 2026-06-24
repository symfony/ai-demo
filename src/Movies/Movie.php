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

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class Movie
{
    /**
     * @param list<array{name: string, role: string}> $cast
     * @param list<string>                            $plot
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly ?int $year,
        public readonly ?string $director,
        public readonly ?string $imdb,
        public readonly array $cast,
        public readonly array $plot,
        public readonly string $rawMarkdown,
    ) {
    }

    public static function fromFile(string $path): self
    {
        $slug = basename($path, '.md');
        $content = file_get_contents($path);
        $title = $slug;
        $year = null;
        if (preg_match('/^#\s+(.+?)(?:\s*\((\d{4})\))?\s*$/m', (string) $content, $matches)) {
            $title = trim($matches[1]);
            $year = isset($matches[2]) ? (int) $matches[2] : null;
        }

        $director = null;
        if (preg_match('/^\*\*Director:\*\*\s*(.+?)\s*$/m', (string) $content, $matches)) {
            $director = trim($matches[1]);
        }

        $imdb = null;
        if (preg_match('/^\*\*IMDB\*\*:\s*(\S+)/m', (string) $content, $matches)) {
            $imdb = trim($matches[1]);
        }

        $cast = [];
        if (preg_match('/^##\s+Cast\s*$(.*?)(?=^##\s+|\z)/ms', (string) $content, $matches)) {
            preg_match_all('/^-\s*\*\*(.+?)\*\*\s+as\s+(.+?)\s*$/m', $matches[1], $castMatches, \PREG_SET_ORDER);
            foreach ($castMatches as $castMatch) {
                $cast[] = [
                    'name' => trim($castMatch[1]),
                    'role' => trim($castMatch[2]),
                ];
            }
        }

        $plot = [];
        if (preg_match('/^##\s+Plot\s*$(.*?)(?=^##\s+|\z)/ms', (string) $content, $matches)) {
            foreach (preg_split('/\n{2,}/', trim($matches[1])) ?: [] as $paragraph) {
                $paragraph = trim($paragraph);
                if ('' !== $paragraph) {
                    $plot[] = $paragraph;
                }
            }
        }

        return new self($slug, $title, $year, $director, $imdb, $cast, $plot, (string) $content);
    }

    public function hue(): int
    {
        return crc32($this->slug) % 360;
    }
}
