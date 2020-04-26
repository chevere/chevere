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

namespace Chevere\Components\Console\Commands;

use Ahc\Cli\Input\Command;
use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Controller\Exceptions\ControllerArgumentsRequiredException;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Ds\Map;

final class ControllerRunCommand extends Command
{
    public function __construct()
    {
        parent::__construct('conrun', 'Runs a controller');

        $this
            ->argument('<fqn>', 'Controller full-qualified name')
            ->option('-a --args', 'Controller arguments <json>')
            ->option('-n --no-hooks', 'No hooks', 'boolval', null);
    }

    public function execute()
    {
        $controllerName = (new ControllerName($this->fqn))->toString();
        /**
         * @var ControllerInterface $controller
         */
        $controller = new $controllerName;
        $args = $this->args;
        if ($args !== null) {
            if ($controller->parameters()->map()->count() == 0) {
                $this->writer()->error('This controller takes no arguments', true);

                return 127;
            }
            $args = json_decode($this->args, true);
            if ($args === null) {
                $this->writer()->error('Invalid arguments JSON string passed', true);

                return 127;
            }
        }
        try {
            $arguments = new ControllerArguments($controller->parameters(), new Map($args ?? []));
        } catch (ControllerArgumentsRequiredException $e) {
            $this->writer()->error('Missing arguments', true);
            $params = [];
            /**
             * @var ControllerParameterInterface $parameter
             */
            foreach ($controller->parameters()->map() as $parameter) {
                $brackets = $parameter->isRequired()
                    ? ['<', '>'] : '["[", "]"]';
                $params[$parameter->name()] = $brackets[0] . 'value' . $brackets[1];
            }
            $this->writer()->colors('<blackBgYellow>' . json_encode($params) . '</end>', true);

            return 127;
        }
        $this->writer()->colors('<green>Run ' . $controllerName . '</end>', true);
        $runner = new ControllerRunner($controller);
        $ran = $runner->run($arguments);
        $this->writer()->write(implode('', $ran->data()));

        return $ran->code();
    }
}
