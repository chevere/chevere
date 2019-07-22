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
// TODO: Must fix the argument typehint, maybe do combo with DI

namespace Chevere\Console\Commands;

use Chevere\App\App;
use Chevere\File;
use Chevere\Path;
use Chevere\Console\Command;
use Chevere\VarDumper\PlainVarDumper;

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
        $callableInput = (string) $this->cli->input->getArgument('callable');

        if (is_callable($callableInput) || class_exists($callableInput)) {
            $callable = $callableInput;
        } else {
            $callable = Path::fromHandle($callableInput);
            if (!File::exists($callable)) {
                $this->cli->io->error(sprintf('Unable to locate callable %s', $callable));

                return 0;
            }
        }
        // Pass explicit callables, "weird" callables (Class::__invoke) runs in the App.
        if (is_callable($callable)) {
            $return = $callable(...$this->cli->input->getOption('argument'));
            $this->cli->io->block(PlainVarDumper::out($return), 'RETURN', 'fg=black;bg=green', ' ', true);
        } else {
            $arguments = $this->cli->input->getOption('argument');
            // argument was declared as array
            if (is_array($arguments)) {
                $app->setArguments($arguments);
            }
            $app->setCallable($callable)->run();
        }

        return 1;
    }
}
