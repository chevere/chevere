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
use JakubOnderka\PhpConsoleColor\ConsoleColor;
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
    const HELP = 'Outputs <fg=magenta>type</>, callable return, and <fg=yellow>buffer</> (if exists)';

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
            'return',
            'r',
            Command::OPTION_NONE,
            'Output return',
        ],
        [
            'buffer',
            'b',
            Command::OPTION_NONE,
            'Output buffer',
        ],
        [
            'noformat',
            'x',
            Command::OPTION_NONE,
            'No output type nor decorations',
        ],
    ];

    /** @var LoaderContract */
    private $loader;

    /** @var string */
    private $callable;

    /** @var array */
    protected $argument;

    /** @var mixed */
    private $return;

    /** @var string */
    private $export;

    /** @var string */
    private $buffer;

    /** @var bool */
    private $isNoFormat;

    /** @var bool */
    private $isReturn;

    /** @var bool */
    private $isBuffer;

    /** @var array */
    private $lines;

    public function callback(LoaderContract $loader): int
    {
        $this->loader = $loader;
        $this->callable = (string) $this->getArgument('callable');
        $this->argument = $this->getOption('argument');

        if (is_subclass_of($this->callable, Controller::class)) {
            $this->runController();
        } else {
            $this->runCallable();
        }

        return 1;
    }

    private function runController(): void
    {
        $this->loader->setArguments($this->argument);
        $this->loader->setController($this->callable);
        $this->loader->run();
    }

    private function runCallable(): void
    {
        $this->validateCallable();
        $this->bufferedRunCallable();

        $this->isNoFormat = (bool) $this->getOption('noformat');
        $this->isReturn = (bool) $this->getOption('return');
        $this->isBuffer = (bool) $this->getOption('buffer');

        if (!$this->isReturn && !$this->isBuffer) {
            $this->isReturn = true;
            $this->isBuffer = true;
        }

        $this->setLines();

        $this->cli()->style()->writeln($this->lines);
    }

    private function validateCallable(): void
    {
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
    }

    /**
     * Run the callable capturing its return and buffer.
     * 
     * Sets @var mixed $return @var string $buffer @var string $export
     */
    private function bufferedRunCallable(): void
    {
        ob_start();
        $callable = $this->callable;
        $this->return = $callable(...$this->argument);
        $buffer = ob_get_contents();
        if (false !== $buffer) {
            $this->buffer = $buffer;
        }
        ob_end_clean();
        $this->export = var_export($this->return, true);
    }

    private function setLines(): void
    {
        $this->lines = [];
        $cc = new ConsoleColor();
        if ($this->isReturn) {
            if ($this->isNoFormat) {
                $this->lines = [$this->export];
            } else {
                $this->lines = ['<fg=magenta>' . $cc->apply('italic', gettype($this->return)) . '</> ' . $this->export];
            }
        }
        if ($this->isBuffer && $this->buffer != '') {
            if ($this->isNoFormat) {
                $this->lines[] = $this->buffer;
            } else {
                $this->lines[] = '<fg=yellow>' . $this->buffer . '</>';
            }
        }
    }
}
