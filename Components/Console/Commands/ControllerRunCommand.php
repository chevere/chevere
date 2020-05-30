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
use Chevere\Exceptions\Controller\ControllerArgumentsRequiredException;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\Controller\ControllerInterface;

final class ControllerRunCommand extends Command
{
    public function __construct()
    {
        parent::__construct('conrun', 'Run a controller');

        $this
            ->argument('<controller>', 'The controller full-qualified name')
            ->option('-a --args', 'Controller arguments as JSON')
            ->option('-p --plugs', 'A list of full-qualified plugs to implement [plug...]', 'is_array', [])
            ->usage(
                '<bold>  conrun</end> <comment><controller></end> ## Without anything<eol/>' .
                '<bold>  conrun</end> <comment><controller> -a \'{"parameter": "argument"}\'</end> ## With arguments<eol/>' .
                '<bold>  conrun</end> <comment><controller> -p App\Plugs\SomePlug App\Plugs\OtherPlug</end> ## With plugs<eol/>'
            );
    }

    public function execute()
    {
        try {
            $controllerName = new ControllerName($this->controller);
        } catch (Exception $e) {
            $this->writer()->error($e->getMessage(), true);

            return 127;
        }
        $controllerNameStr = $controllerName->toString();
        /**
         * @var ControllerInterface $controller
         */
        $controller = new $controllerNameStr;
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
            $arguments = new ControllerArguments($controller->parameters(), $args ?? []);
        } catch (ControllerArgumentsRequiredException $e) {
            $this->writer()->error('Missing arguments', true);

            return 127;
        }
        $this->writer()->ok('Run ' . $controllerNameStr, true);
        $runner = new ControllerRunner($controller);
        $ran = $runner->ran($arguments);
        $this->writer()->write(implode('', $ran->data()));

        return $ran->code();
    }
}
