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

use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Countable;
use Generator;

/**
 * Describes the component in charge of defining a collection of chained tasks.
 */
interface WorkflowInterface extends Countable
{
    const REGEX_PARAMETER_REFERENCE = '/^\${([\w-]*)}$/';
    const REGEX_STEP_REFERENCE = '/^\${([\w-]*)\:([\w-]*)}$/';

    public function __construct(string $name);

    /**
     * Provides access to the instance name.
     */
    public function name(): string;

    /**
     * Return an instance with the specified `$step`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step`.
     *
     * @throws OverflowException
     */
    public function withAdded(StepInterface ...$step): WorkflowInterface;

    /**
     * Return an instance with the specified `$step` added before `$before`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step` added before `$before`.
     *
     * @throws OverflowException
     */
    public function withAddedBefore(string $before, StepInterface ...$step): WorkflowInterface;

    /**
     * Return an instance with the specified `$step` added after `$after`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$step` added after `$after`.
     *
     * @throws OverflowException
     */
    public function withAddedAfter(string $after, StepInterface ...$step): WorkflowInterface;

    public function has(string $step): bool;

    public function get(string $step): StepInterface;

    public function parameters(): ParametersInterface;

    public function order(): array;

    public function hasVar(string $var): bool;

    /**
     * Provides access to the `$var` mapping for job variables.
     *
     * Case `${foo}` (workflow parameters):
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
    public function getExpected(string $step): array;

    /**
     * @return Generator<string, StepInterface>
     */
    public function getGenerator(): Generator;
}
