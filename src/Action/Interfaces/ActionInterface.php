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
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Response\Interfaces\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
 */
interface ActionInterface extends DescriptionInterface
{
    public function __construct();

    /**
     * Defines action parameters.
     */
    public function getParameters(): ParametersInterface;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public function getResponseParameters(): ParametersInterface;

    /**
     * Method called when running the action.
     */
    public function run(ArgumentsInterface $arguments): ResponseInterface;

    /**
     * Provides access to the parameters.
     */
    public function parameters(): ParametersInterface;

    /**
     * Provides access to the expected response data parameters.
     */
    public function responseParameters(): ParametersInterface;

    public function getArguments(mixed ...$namedArguments): ArgumentsInterface;

    /**
     * Retrieves a new success response with type-hinted data.
     */
    public function getResponse(mixed ...$namedData): ResponseInterface;
}
