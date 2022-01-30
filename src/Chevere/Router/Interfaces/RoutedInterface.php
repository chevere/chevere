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

namespace Chevere\Router\Interfaces;

use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Controller\Interfaces\ControllerNameInterface;

/**
 * Describes the component in charge of defining a routed route.
 */
interface RoutedInterface
{
    public function __construct(ControllerNameInterface $controllerName, array $arguments);

    /**
     * Provides access to the `$controllerName` instance.
     */
    public function controllerName(): ControllerNameInterface;

    /**
     * Provides access to a new `$controllerName` instance.
     */
    public function getController(): ControllerInterface;

    /**
     * Provides access to the `$arguments` instance.
     *
     * ```php
     * return [
     *     'name' => 'value',
     * ]
     * ```
     */
    public function arguments(): array;
}
