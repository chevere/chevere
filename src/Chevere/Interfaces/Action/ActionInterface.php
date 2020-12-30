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

use Chevere\Interfaces\Common\DescriptionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
 */
interface ActionInterface extends DescriptionInterface
{
    public function __construct();

    /**
     * Defines parameters.
     */
    public function getParameters(): ParametersInterface;

    /**
     * Defines expected response data parameters when executing `run` method.
     */
    public function getResponseDataParameters(): ParametersInterface;

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
    public function responseDataParameters(): ParametersInterface;

    public function getArguments(mixed ...$arguments): ArgumentsInterface;

    /**
     * Retrieves a new success response with type-hinted data.
     *
     * @param array<string, mixed> $data
     */
    public function getResponse(mixed ...$data): ResponseInterface;
}
