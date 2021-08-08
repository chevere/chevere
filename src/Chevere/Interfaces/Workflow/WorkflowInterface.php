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

namespace Chevere\Interfaces\Workflow;

use Chevere\Components\DataStructure\Map;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Dependent\DependenciesInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Countable;
use Generator;

/**
 * Describes the component in charge of defining a collection of chained tasks.
 */
interface WorkflowInterface extends Countable
{
    public const REGEX_PARAMETER_REFERENCE = '/^\${([\w-]*)}$/';

    public const REGEX_STEP_REFERENCE = '/^\${([\w-]*)\:([\w-]*)}$/';

    public function __construct(StepInterface ...$steps);

    public function vars(): Map;

    /**
     * Return an instance with the specified `$step`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step`.
     *
     * @throws OverflowException
     */
    public function withAdded(StepInterface ...$steps): self;

    /**
     * Return an instance with the specified `$step` added before `$before`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step` added before `$before`.
     *
     * @throws OverflowException
     */
    public function withAddedBefore(string $before, StepInterface ...$step): self;

    /**
     * Return an instance with the specified `$step` added after `$after`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step` added after `$after`.
     *
     * @throws OverflowException
     */
    public function withAddedAfter(string $after, StepInterface ...$step): self;

    public function has(string $step): bool;

    public function get(string $step): StepInterface;

    public function dependencies(): DependenciesInterface;

    public function parameters(): ParametersInterface;

    public function order(): array;

    /**
     * Provides access to the `$var` mapping for job variables.
     *
     * Case `${foo}` (workflow variables):
     *
     * ```php
     * return ['foo'];
     * ```
     *
     * Case `${step:var}` (named step responses):
     *
     * ```php
     * return ['step', 'var'];
     * ```
     *
     * @return string[]
     */
    public function getVar(string $var): array;

    /**
     * Provides access to the expected return arguments for the given `$step`.
     */
    public function getProvided(string $step): ParametersInterface;

    /**
     * @return Generator<string, StepInterface>
     */
    public function getGenerator(): Generator;
}
