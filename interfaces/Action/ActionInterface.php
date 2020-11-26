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

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Description\GetDescriptionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

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
     * Defines expected response data parameters when executing `run` method.
     */
    public function getResponseDataParameters(): ParametersInterface;

    /**
     * Provides access to the description.
     */
    public function description(): string;

    /**
     * Provides access to the parameters.
     */
    public function parameters(): ParametersInterface;

    /**
     * Provides access to the expected response data parameters.
     */
    public function responseDataParameters(): ParametersInterface;

    /**
     *
     * @throws OutOfBoundsException
     * @throws TypeException
     */
    public function assertResponseDataParameters(array $arguments): void;

    /**
     * Method called when running the action. This method MUST not alter the state of the instance.
     */
    public function run(ArgumentsInterface $arguments): ResponseInterface;
}
