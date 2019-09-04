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

namespace Chevere\Console\Commands;

use Closure;
use Chevere\Console\Command;
use Chevere\VarDump\PlainVarDump;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Controller\Controller;
use Chevere\Message;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\FormatterHelper;

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
    const HELP = 'Outputs <fg=magenta>(type)</> <fg=blue>$return</> line and <fg=yellow>buffer</> (if exists)';

    const ARGUMENTS = [
        ['callable', Command::ARGUMENT_REQUIRED, 'A fully-qualified callable name or Controller'],
    ];

    const OPTIONS = [
        [
            'argument',
            'a',
            Command::OPTION_OPTIONAL | Command::OPTION_IS_ARRAY,
            'Callable arguments (in declarative order)',
        ],
        [
            'plain',
            'p',
            Command::OPTION_NONE,
            'Plain return line and output (if exists)',
        ],
    ];

    /** @var LoaderContract */
    private $loader;

    /** @var string */
    private $callable;

    /** @var array */
    private $arguments;

    public function callback(LoaderContract $loader): int
    {
        $this->loader = $loader;
        $this->callable = (string) $this->cli->input()->getArgument('callable');
        $this->arguments = $this->cli->input()->getOption('argument');

        if (is_subclass_of($this->callable, Controller::class)) {
            $this->runController();
        } else {
            if (class_exists($this->callable)) {
                $isCallable = method_exists($this->callable, '__invoke');
            } else {
                $isCallable = is_callable($this->callable);
            }
            if (!$isCallable) {
                throw new InvalidArgumentException(
                    (new Message('No callable found for %s string.'))
                        ->code('%s', $this->callable)
                        ->toString()
                );
            }

            ob_start();
            $callable = $this->callable;
            $return = $callable(...$this->arguments);
            $buffer = ob_get_contents();
            ob_end_clean();

            $export = var_export($return, true);
            $isBonito = (bool) !$this->cli->input()->getOption('plain');

            if ($isBonito) {
                $lines = ['<fg=magenta>(' . gettype($return) . ')</> <fg=blue>' . $export . '</>'];
            } else {
                $lines = [$export];
            }
            if ($buffer != '') {
                $lines[] = !$isBonito ? $buffer : ('<fg=yellow>' . $buffer . '</>');
            }

            $this->cli->style()->writeln($lines);
        }

        return 1;
    }

    private function runController(): int
    {
        $this->loader->setArguments($this->arguments);
        $this->loader->setController($this->callable);
        $this->loader->run();
        return 0;
    }
}
