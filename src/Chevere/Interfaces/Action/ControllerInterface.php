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

use Chevere\Components\Type\Type;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

/**
 * Describes the component in charge of defining a controller, which is an action
 * intended to be exposed closest to an application entry-point HTTP/CLI mapping.
 *
 * Key point of a controller is that it only takes string arguments and it
 * provides an additional layer for context parameters.
 */
interface ControllerInterface extends ActionInterface
{
    public const PARAMETER_TYPE = Type::STRING;

    /**
     * Defines context parameters.
     */
    public function getContextParameters(): ParametersInterface;

    public function withContextArguments(mixed ...$namedArguments): self;

    public function contextArguments(): ArgumentsInterface;

    public function hasContextArguments(): bool;

    /**
     * Provides access to context parameters.
     */
    public function contextParameters(): ParametersInterface;
}
