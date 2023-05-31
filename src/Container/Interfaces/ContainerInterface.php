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

namespace Chevere\Container\Interfaces;

use Chevere\DataStructure\Interfaces\MappedInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Describes the component in charge of defining the workflow container interface.
 *
 * @extends MappedInterface<mixed>
 */
interface ContainerInterface extends MappedInterface, PsrContainerInterface
{
    /**
     * Return an instance with the specified value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified value.
     */
    public function withPut(string $key, mixed $value): self;
}
