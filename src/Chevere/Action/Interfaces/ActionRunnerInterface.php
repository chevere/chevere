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

use Chevere\Controller\Interfaces\ControllerInterface;

/**
 * Describes the component in charge of running the controller.
 */
interface ActionRunnerInterface
{
    public function __construct(ControllerInterface $controller);

    /**
     * Executes the controller with the given `$arguments`.
     */
    public function execute(array $arguments): ActionExecutedInterface;
}
