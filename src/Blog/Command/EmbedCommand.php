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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:blog:embed', description: 'Create embeddings for Symfony blog and push to ChromaDB.')]
final class EmbedCommand extends Command
{
    public function __construct(
        private readonly Embedder $embedder,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Loading RSS of Symfony blog as embeddings into ChromaDB');

        $this->embedder->embedBlog();

        $io->success('Symfony Blog Successfully Embedded!');

        return Command::SUCCESS;
    }
}
