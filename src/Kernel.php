<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use App\Platform\AmazeeAiPlatformFactory;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        // Replace the generic platform factory to use a ResultConverter that
        // handles LiteLLM returning finish_reason "tool_calls" for structured output.
        $container->addCompilerPass(new class() implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                if (!$container->hasDefinition('ai.platform.generic.amazeeai')) {
                    return;
                }

                $container->getDefinition('ai.platform.generic.amazeeai')
                    ->setFactory([AmazeeAiPlatformFactory::class, 'create']);
            }
        });
    }

    /**
     * @return array<string, array<mixed>|bool|string|int|float|\UnitEnum|null>
     */
    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();

        // Disable generation of config/reference.php - not needed in this app
        unset($parameters['.kernel.bundles_definition']);

        return $parameters;
    }
}
