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

namespace Chevere\Interfaces\Controller;

use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

/**
 * Describes the component in charge of handling controller instructions.
 */
interface ControllerInterface extends ActionInterface
{
    /**
     * Provides access to the controller parameters defined with `getParameters()`.
     */
    public function parameters(): ParametersInterface;

    /**
     * Defines the default description.
     */
    public function getDescription(): string;

    /**
     * Returns a new instance with setup made. Useful to wrap pluggable instructions on parameters and description.
     */
    public function setUp(): ControllerInterface;
}
