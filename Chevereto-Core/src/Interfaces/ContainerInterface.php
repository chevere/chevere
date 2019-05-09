<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Interfaces;

interface ContainerInterface
{
    public function hasObjectsDefinition(): bool;

    public function __call(string $name, array $arguments = null);
}
