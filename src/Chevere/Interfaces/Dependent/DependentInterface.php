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

namespace Chevere\Interfaces\Dependent;

use Chevere\Exceptions\Core\LogicException;

/**
 * Describes the component in charge of defining a class with explicit dependencies.
 */
interface DependentInterface
{
    /**
     * Dependencies must be passed on construct for this interface.
     *
     * Each named argument value will be assigned to a property of the
     * same name.
     *
     * ```php
     * class Dependent Implements DependentInterface
     * {
     *      private FooType $foo;
     *      private BarType $bar;
     *      // ...
     * }
     *
     * new Dependent(foo: $fooInstance, bar: $barInstance);
     * ```
     *
     * @param object $namedDependency Named dependency `name: $var,`
     */
    public function __construct(object ...$namedDependency);

    /**
     * Declares required dependencies as class name -> property name.
     */
    public function getDependencies(): DependenciesInterface;

    /**
     * Asserts that the instance meets all dependencies.
     *
     * @throws LogicException
     */
    public function assertDependencies(): void;

    /**
     * Provides access to the dependencies instance.
     */
    public function dependencies(): DependenciesInterface;
}
