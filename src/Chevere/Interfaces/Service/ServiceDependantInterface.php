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

namespace Chevere\Interfaces\Service;

use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;

/**
 * Describes the component in charge of defining a class with explicit dependencies.
 */
interface ServiceDependantInterface
{
    /**
     * Return an instance with the specified dependencies.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified dependencies.
     *
     * Each named argument value will be assigned to an object property of the same name.
     */
    public function withDependencies(mixed ...$namedArguments): self;

    /**
     * Declares required dependencies as class name -> property name.
     */
    public function getDependencies(): ClassMapInterface;

    /**
     * Asserts that the instance meets all dependencies.
     *
     * @throws LogicException
     */
    public function assertDependencies(): void;
}
