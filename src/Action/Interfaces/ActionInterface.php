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

use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Response\Interfaces\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
 *
 * @method array<string, mixed> run() Defines the action run
 * logic.
 */
interface ActionInterface
{
    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public static function acceptResponse(): ParameterInterface;

    /**
     * Retrieves a new response instance typed against the defined response data parameters.
     *
     * This method will provide a response instance with data provided by
     * executing the `run` method against `$namedArguments`.
     */
    public function getResponse(mixed ...$argument): ResponseInterface;

    public function assert(): void;
}
