<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// TODO: Deprecate callables by file

namespace Chevereto\Chevere\Commands;

use Chevereto\Chevere\App;
use Chevereto\Chevere\VarDumper\PlainVarDumper;
use Chevereto\Chevere\File;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Command;

/**
 * The RunCommand allows to run any callable present in the app.
 *
 * Usage:
 * php app/console run <pathHandle>
 */
class RunCommand extends Command
{
    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
            ->setDescription('Run any callable')
            ->setHelp('This command allows you to run any callable')
            ->addArgument('callable', Command::ARGUMENT_REQUIRED, 'The callable handle (name, fileHandle)')
            ->addOption(
                'argument',
                'a',
                Command::OPTION_OPTIONAL | Command::OPTION_IS_ARRAY,
                'Callable arguments (in declarative order)'
            );
    }

    /**
     * Run ANY callable.
     */
    public function callback(App $app): int
    {
        $cli = $this->getCli();
        $input = $cli->getInput();
        $callableInput = (string) $input->getArgument('callable');

        if (is_callable($callableInput) || class_exists($callableInput)) {
            $callable = $callableInput;
        } else {
            $callable = Path::fromHandle($callableInput);
            if (!File::exists($callable)) {
                $cli->getIo()->error(sprintf('Unable to locate callable %s', $callable));

                return 0;
            }
        }
        // Pass explicit callables, "weird" callables (Class::__invoke) runs in the App.
        if (is_callable($callable)) {
            // TODO: Must fix the argument typehint, maybe do combo with DI
            $return = $callable(...$input->getOption('argument'));
            $cli->getIo()->block(PlainVarDumper::out($return), 'RETURN', 'fg=black;bg=green', ' ', true);
        } else {
            $arguments = $input->getOption('argument');
            // argument was declared as array
            if (is_array($arguments)) {
                $app->setArguments($arguments);
            }
            $app->setCallable($callable)->run();
        }

        return 1;
    }
}
