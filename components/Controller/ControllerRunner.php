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

use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerRanInterface;
use Chevere\Components\Message\Message;
use Exception;
use Throwable;

final class ControllerRunner
{
    private ControllerInterface $controller;

    private string $controllerName;

    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->controllerName = get_class($this->controller);
    }

    public function run(ControllerArgumentsInterface $controllerArguments): ControllerRanInterface
    {
        try {
            $code = 0;
            $data = [];
            try {
                $this->controller->setUp();
            } catch (Throwable $t) {
                throw new Exception('setUp');
            }
            try {
                $response = $this->controller->run($controllerArguments);
            } catch (Throwable $t) {
                throw new Exception('run');
            }
            try {
                $this->controller->tearDown();
            } catch (Throwable $t) {
                throw new Exception('tearDown');
            }
            $data = $response->data();

            return new ControllerRan($data);
        } catch (Exception $e) {
            $data = [
                (new Message('Throwable %throwable% catched when running %method%'))
                    ->code('%throwable%', get_class($t))
                    ->code('%method%', $this->controllerName . '::' . $e->getMessage())
                    ->toString()
            ];

            return (new ControllerRan($data))
                ->withThrowable($t);
        }
    }
}
