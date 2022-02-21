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
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\StringParameter;
use Chevere\Response\Interfaces\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
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

    public function __construct();

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
     * Retrieves an arguments instance typed against the action parameters.
     */
    public function getArguments(mixed ...$namedArguments): ArgumentsInterface;

    /**
     * Retrieves a new success response with type-hinted data.
     */
    public function getResponse(mixed ...$namedData): ResponseInterface;
}
