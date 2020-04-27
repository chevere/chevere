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

namespace Chevere\Components\Controller\Interfaces;

use Ds\Map;

interface ControllerParametersInterface
{
    /**
     * @return Map [<string>name => <string>regex,]
     */
    public function map(): Map;

    public function withPut(ControllerParameterInterface $controllerParameter): ControllerParametersInterface;

    public function hasParameter(string $name): bool;

    public function get(string $name): ControllerParameterInterface;
}
