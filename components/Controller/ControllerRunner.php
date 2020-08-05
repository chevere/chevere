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

namespace Chevere\Components\Controller;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Controller\ControllerRunnerInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Throwable;

final class ControllerRunner implements ControllerRunnerInterface
{
    private ControllerInterface $controller;

    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    public function execute(ArgumentsInterface $arguments): ControllerExecutedInterface
    {
        $this->assertArguments($arguments);
        try {
            $response = $this->controller->run($arguments);
        } catch (Throwable $e) {
            return (new ControllerExecuted([]))->withThrowable($e, 1);
        }

        return new ControllerExecuted($response->data());
    }

    private function assertArguments(ArgumentsInterface $arguments): void
    {
        if ($this->controller->parameters()->toArray() !== $arguments->parameters()->toArray()) {
            $expected = array_keys($this->controller->parameters()->toArray());
            $provided = array_keys($arguments->parameters()->toArray());
            sort($expected);
            sort($provided);
            throw new LogicException(
                (new Message('Expecting %expected% argument(s) but %provided% provided'))
                    ->code('%expected%', $expected === [] ? 'none' : implode(', ', $expected))
                    ->code('%provided%', $provided === [] ? 'none' : implode(', ', $provided))
            );
        }
    }
}
