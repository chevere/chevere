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

use Chevere\Parameter\ArrayParameter;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\StringParameter;
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
     * @var array<string, string>
     */
    public const TYPE_TO_PARAMETER = [
        'array' => ArrayParameter::class,
        'bool' => BooleanParameter::class,
        'float' => FloatParameter::class,
        'int' => IntegerParameter::class,
        'string' => StringParameter::class,
        'object' => ObjectParameter::class,
    ];

    /**
     * Determines if action is strict or not.
     *
     * When the action is strict the `run` method return value will be matched
     * against the defined response parameters at `acceptResponseParameter`.
     */
    public static function isStrict(): bool;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public static function acceptResponse(): ArrayTypeParameterInterface;

    /**
     * Retrieves a new response instance typed against the defined response data parameters.
     *
     * This method will provide a response instance with data provided by
     * executing the `run` method against `$namedArguments`.
     */
    public function getResponse(mixed ...$argument): ResponseInterface;

    public static function description(): string;

    public static function getParameters(): ParametersInterface;
}
