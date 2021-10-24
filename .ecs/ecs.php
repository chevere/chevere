<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/ecs-chevere.php');
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, [
        __DIR__ . '/vendor/*',
        __DIR__ . '/tests/Cache/_resources/*',
        __DIR__ . '/tests/Filesystem/_resources/*',
        __DIR__ . '/tests/Pluggable/_resources/*',
    ]);
};
