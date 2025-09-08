<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Blog\Command;

use App\Blog\Embedder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:blog:embed', description: 'Create embeddings for Symfony blog and push to ChromaDB.')]
final class EmbedCommand
{
    public function __construct(
        private readonly Embedder $embedder,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $io->title('Loading RSS of Symfony blog as embeddings into ChromaDB');

        $this->embedder->embedBlog();

        $io->success('Symfony Blog Successfully Embedded!');

        return Command::SUCCESS;
    }
}
