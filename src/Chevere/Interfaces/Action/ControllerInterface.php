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

namespace Chevere\Interfaces\Action;

use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

/**
 * Describes the component in charge of defining a controller, which is an action
 * but with fixed parameters type.
 */
interface ControllerInterface extends ActionInterface
{
    public function getParametersTypeName(): string;

    public function assertParametersType(): void;

    /**
     * Provides access to the actual controller parameters (after hooks, if any).
     */
    public function parameters(): ParametersInterface;

    /**
     * Returns a new instance with setup made. Useful to wrap pluggable instructions on parameters and description.
     */
    public function withSetUp(): ControllerInterface;
}
