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

use Chevere\File;
use Chevere\Path;
use Chevere\Console\Command;
use Chevere\VarDump\PlainVarDump;
use Chevere\Contracts\App\LoaderContract;

/**
 * The RunCommand allows to run any callable present in the app.
 *
 * Usage:
 * php app/console run <pathHandle>
 */
final class RunCommand extends Command
{
    const NAME = 'run';
    const DESCRIPTION = 'Run any callable';
    const HELP = 'This command allows you to run any callable';

    const ARGUMENTS = [
        ['callable', Command::ARGUMENT_REQUIRED, 'A fully-qualified callable name'],
    ];

    const OPTIONS = [
        [
            'argument',
            'a',
            Command::OPTION_OPTIONAL | Command::OPTION_IS_ARRAY,
            'Callable arguments (in declarative order)',
        ],
    ];

    public function callback(LoaderContract $loader): int
    {
        $callableInput = (string) $this->cli->input->getArgument('callable');

        if (is_callable($callableInput) || class_exists($callableInput)) {
            $callable = $callableInput;
        } else {
            $callable = Path::fromHandle($callableInput);
            if (!File::exists($callable)) {
                $this->cli->out->error(sprintf('Unable to locate callable %s', $callable));

                return 0;
            }
        }
        // Pass explicit callables, "weird" callables (Class::__invoke) runs in the App.
        if (is_callable($callable)) {
            $return = $callable(...$this->cli->input->getOption('argument'));
            $this->cli->out->block(PlainVarDump::out($return), 'RETURN', 'fg=black;bg=green', ' ', true);
        } else {
            $arguments = $this->cli->input->getOption('argument');
            // argument was declared as array
            if (is_array($arguments)) {
                $loader->setArguments($arguments);
            }
            $loader->setController($callable);
            $loader->run();
        }

        return 1;
    }
}
