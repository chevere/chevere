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
use Chevere\Parameter\ArrayParameter;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\StringParameter;
use Chevere\Response\Interfaces\ResponseInterface;
use Psr\Container\ContainerInterface;

/**
 * Describes the component in charge of defining a single action.
 *
 * @method array<string, mixed> run(mixed ...$arguments) Defines the action run logic.
 */
interface ActionInterface extends DescriptionInterface
{
    public const TYPE_TO_PARAMETER = [
        'array' => ArrayParameter::class,
        'bool' => BooleanParameter::class,
        'float' => FloatParameter::class,
        'int' => IntegerParameter::class,
        'string' => StringParameter::class,
        'object' => ObjectParameter::class,
    ];
    
    /**
     * Defines expected container parameters when executing `run` method.
     */
    public function getContainerParameters(): ParametersInterface;

    /**
     * Provides access to the expected container parameters.
     */
    public function containerParameters(): ParametersInterface;

    public function withContainer(ContainerInterface $container): ActionInterface;

    /**
     * Provides access to the container.
     */
    public function container(): ContainerInterface;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public function getResponseParameters(): ParametersInterface;

    /**
     * Method called when running the action.
     */
    // public function run({...}): ResponseInterface;

    /**
     * Provides access to the parameters.
     */
    public function parameters(): ParametersInterface;

    /**
     * Provides access to the expected response data parameters.
     */
    public function responseParameters(): ParametersInterface;

    /**
     * Retrieves a new response instance typed against the defined response data parameters.
     *
     * This method will provide a response instance with data provided by
     * executing the `run` method against `$namedArguments`.
     */
    public function getResponse(mixed ...$namedArguments): ResponseInterface;
}
