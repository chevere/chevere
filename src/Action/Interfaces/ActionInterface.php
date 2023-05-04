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

namespace Chevere\Action\Interfaces;

use Chevere\Common\Interfaces\DescribedInterface;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ParametersAccessInterface;
use Chevere\Response\Interfaces\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
 *
 * @method array<string, mixed> run() Defines the action run
 * logic.
 */
interface ActionInterface extends ParametersAccessInterface, DescribedInterface
{
    /**
     * Determines if action is strict or not.
     *
     * When the action is strict the `run` method return value will be matched
     * against the defined response parameters at `acceptResponseParameter`.
     */
    public function isStrict(): bool;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public function acceptResponse(): ArrayTypeParameterInterface;

    /**
     * Retrieves a new response instance typed against the defined response data parameters.
     *
     * This method will provide a response instance with data provided by
     * executing the `run` method against `$namedArguments`.
     */
    public function getResponse(mixed ...$argument): ResponseInterface;
}
