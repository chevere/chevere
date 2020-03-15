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

namespace Chevere\Components\Http\Interfaces;

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\MethodControllerObjectsRead;

interface MethodControllersInterface
{
    public function __construct(MethodControllerInterface ...$methodController);

    /**
     * Return an instance with the specified added MethodControllerInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified MethodControllerInterface.
     *
     * Note: This method overrides any method already added.
     */
    public function withAddedMethodController(MethodControllerInterface $methodController): MethodControllersInterface;

    /**
     * Returns a boolean indicating whether the instance has any MethodInterface.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has the given MethodInterface.
     */
    public function hasMethod(MethodInterface $method): bool;

    /**
     * @throws MethodNotFoundException
     */
    public function getMethod(MethodInterface $method): MethodControllerInterface;

    public function objects(): MethodControllerObjectsRead;
}
