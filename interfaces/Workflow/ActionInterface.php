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

namespace Chevere\Interfaces\Workflow;

use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

/**
 * Describes the component in charge of defining a single action.
 */
interface ActionInterface
{
    public function __construct();

    /**
     * Defines the default parameters.
     */
    public function getParameters(): ParametersInterface;

    /**
     * Method called when running the action. This method MUST not alter the state of the instance.
     */
    public function run(ArgumentsInterface $arguments): ResponseInterface;
}
