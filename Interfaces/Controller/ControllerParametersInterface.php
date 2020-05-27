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

namespace Chevere\Interfaces\Controller;

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Ds\Map;

interface ControllerParametersInterface extends DsMapInterface
{
    /**
     * @return Map [<string>name => <string>regex,]
     */
    public function map(): Map;

    /**
     * Return an instance with the specified Controller Parameter.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Controller Parameter.
     */
    public function withParameter(ControllerParameterInterface $controllerParameter): ControllerParametersInterface;

    public function hasParameterName(string $name): bool;

    public function get(string $name): ControllerParameterInterface;
}
