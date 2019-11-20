<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Router;

interface RouterPropertiesContract
{
    /**
     * Creates a new instance.
     */
    public function __construct();

    /**
     * Return an instance with the specified regex string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified regex string.
     */
    public function withRegex(string $regex): RouterPropertiesContract;

    /**
     * Provides access to the regex string.
     */
    public function regex(): string;

    /**
     * Return an instance with the specified routes array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified routes array.
     */
    public function withRoutes(array $routes): RouterPropertiesContract;

    /**
     * Provides access to the routes array.
     */
    public function routes(): array;

    /**
     * Return an instance with the specified index array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified index array.
     */
    public function withIndex(array $index): RouterPropertiesContract;

    /**
     * Provides access to the index array.
     */
    public function index(): array;
}
