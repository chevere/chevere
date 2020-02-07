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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;

interface RouterPropertiesInterface extends ToArrayInterface
{
    /**
     * Return an instance with the specified regex string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified regex string.
     *
     * @param string $regex see RegexPropertyInterface
     */
    public function withRegex(string $regex): RouterPropertiesInterface;

    /**
     * Returns a boolean indicating whether the instance has a regex string.
     */
    public function hasRegex(): bool;

    /**
     * Provides access to the regex string. The representation used when resolving routing.
     */
    public function regex(): string;

    // /**
    //  * Return an instance with the specified routes array.
    //  *
    //  * This method MUST retain the state of the current instance, and return
    //  * an instance that contains the specified routes array.
    //  *
    //  * @param array $routes see RoutesPropertyInterface
    //  */
    // public function withRoutes(array $routes): RouterPropertiesInterface;

    // /**
    //  * Provides access to the routes array.
    //  */
    // public function routes(): array;

    /**
     * Return an instance with the specified index array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified index array.
     *
     * @param array $index see IndexPropertyInterface
     */
    public function withIndex(array $index): RouterPropertiesInterface;

    /**
     * Provides access to the index array.
     */
    public function index(): array;

    /**
     * Return an instance with the specified groups array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified groups array.
     *
     * @param array $groups see GroupsPropertyInterface
     */
    public function withGroups(array $groups): RouterPropertiesInterface;

    /**
     * Provides access to the groups array.
     */
    public function groups(): array;

    /**
     * Return an instance with the specified named array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified named array.
     *
     * @param array $named see NamedPropertyInterface
     */
    public function withNamed(array $named): RouterPropertiesInterface;

    /**
     * Provides access to the named array.
     */
    public function named(): array;

    /**
     * Checks that all properties are valid (format, not just type).
     *
     * @throws RouterPropertyException if there are errors in the properties
     */
    public function assert(): void;

    /**
     *
     * @return array [name => value]
     */
    public function toArray(): array;
}
