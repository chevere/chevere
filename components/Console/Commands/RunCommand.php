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

use InvalidArgumentException;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevere\Components\Console\Command;
use Chevere\Components\Controller\Controller;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;

/**
 * The RunCommand allows to run any callable present in the app.
 *
 * Usage:
 * php app/console run callable
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

    private BuilderInterface $builder;

    /** @var string */
    private string $callable;

    /** @var array */
    protected array $argument;

    /** @var mixed */
    private $return;

    /** @var string */
    private string $export;

    /** @var string */
    private string $buffer;

    /** @var bool */
    private bool $isNoFormat;

    /** @var bool */
    private bool $isReturn;

    /** @var bool */
    private bool $isBuffer;

    /** @var array */
    private array $lines;

    public function callback(BuilderInterface $builder): int
    {
        $this->builder = $builder;
        $this->callable = $this->getArgumentString('callable');
        $this->argument = $this->getOptionArray('argument');

        if (is_subclass_of($this->callable, Controller::class)) {
            $this->runController();
        } else {
            $this->runCallable();
        }

        return 1;
    }

    private function runController(): void
    {
        $this->builder = $this->builder
            ->withControllerArguments($this->argument)
            ->withControllerName($this->callable);
        $this->builder->run();
    }

    private function runCallable(): void
    {
        $this->validateCallable();
        $this->bufferedRunCallable();

        $this->isNoFormat = (bool) $this->console->input()->getOption('noformat');
        $this->isReturn = (bool) $this->console->input()->getOption('return');
        $this->isBuffer = (bool) $this->console->input()->getOption('buffer');

        if (!$this->isReturn && !$this->isBuffer) {
            $this->isReturn = true;
            $this->isBuffer = true;
        }

        $this->setLines();

        $this->console()->style()->writeln($this->lines);
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
                (new Message('No callable found for %callable% string'))
                    ->code('%callable%', $this->callable)
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
            $this->lines = $this->isNoFormat
                ? [$this->export]
                : ['<fg=magenta>' . $cc->apply('italic', gettype($this->return)) . '</> ' . $this->export];
        }
        if ($this->isBuffer && '' != $this->buffer) {
            if ($this->isNoFormat) {
                $this->lines[] = $this->buffer;

                return;
            }
            $this->lines[] = '<fg=yellow>' . $this->buffer . '</>';
        }
    }
}
