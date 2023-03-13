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

use Chevere\Common\Interfaces\DescriptionInterface;
use Chevere\Parameter\Interfaces\ArrayTypeInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Response\Interfaces\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * Describes the component in charge of defining a single action.
 *
 * @method array<string, mixed> run(mixed ...$argument) Defines the action run
 * logic.
 */
interface ActionInterface extends DescriptionInterface
{
    /**
     * Determines if action is strict or not.
     *
     * When the action is strict the `run` method return value will be matched
     * against the defined response parameters at `getResponseParameters`.
     */
    public function isStrict(): bool;

    /**
     * Defines expected container parameters when executing `run` method.
     */
    public function getContainerParameters(): ParametersInterface;

    /**
     * Provides access to the expected container parameters.
     */
    public function containerParameters(): ParametersInterface;

    /**
     * Return an instance with the specified `$container`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$container`.
     */
    public function withContainer(ContainerInterface $container): static;

    /**
     * Provides access to the container.
     */
    public function container(): ContainerInterface;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public function getResponseParameter(): ArrayTypeInterface;

    /**
     * Provides access to the parameters.
     */
    public function parameters(): ParametersInterface;

    /**
     * Provides access to the expected response parameter.
     */
    public function responseParameter(): ArrayTypeInterface;

    /**
     * Retrieves a new response instance typed against the defined response data parameters.
     *
     * This method will provide a response instance with data provided by
     * executing the `run` method against `$namedArguments`.
     */
    public function getResponse(mixed ...$argument): ResponseInterface;
}
