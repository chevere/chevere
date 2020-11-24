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

use Chevere\Interfaces\Description\GetDescriptionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Type\TypeInterface;

/**
 * Describes the component in charge of defining a single action.
 */
interface ActionInterface extends GetDescriptionInterface
{
    public function __construct();

    /**
     * Defines default parameters.
     */
    public function getParameters(): ParametersInterface;

    /**
     * Defines expected response data types when executing `run` method.
     *
     * @return array<string, TypeInterface>
     */
    public function getResponseDataTypes(): array;

    public function description(): string;

    public function parameters(): ParametersInterface;

    /**
     * @return array<string, TypeInterface>
     */
    public function responseDataTypes(): array;

    public function assertResponseDataTypes(array $namedArguments): void;

    /**
     * Method called when running the action. This method MUST not alter the state of the instance.
     */
    public function run(ArgumentsInterface $arguments): ResponseInterface;
}
