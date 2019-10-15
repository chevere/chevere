<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Console;

use Exception;
use RuntimeException;

use Symfony\Component\Console\Application as Symfony;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Chevere\Components\Console\Commands\BuildCommand;
use Chevere\Components\Console\Commands\ClearLogsCommand;
use Chevere\Components\Console\Commands\DestroyCommand;
use Chevere\Components\Console\Commands\InspectCommand;
use Chevere\Components\Console\Commands\RequestCommand;
use Chevere\Components\Console\Commands\RunCommand;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\ConsoleContract;

/**
 * The built-in Chevere application console.
 */
final class Console implements ConsoleContract
{
    /** @var ArgvInput */
    private $input;

    /** @var ConsoleOutput */
    private $output;

    /** @var CommandContract */
    private $command;

    /** @var string The first argument (command) passed */
    private $commandString;

    /** @var Symfony */
    private $symfony;

    /** @var SymfonyStyle */
    private $style;

    /** @var BuilderContract */
    private $builder;

    public function __construct()
    {
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->commandString = $this->input->getFirstArgument();
        $this->symfony = new Symfony(static::NAME, static::VERSION);
        $this->symfony->setAutoExit(false);
        $this->style = new SymfonyStyle($this->input, $this->output);
        $this->addCommands();
    }

    public function withCommand(CommandContract $command): ConsoleContract
    {
        $new = clone $this;
        $new->command = $command;

        return $new;
    }

    public function hasCommand(): bool
    {
        return isset($this->command);
    }

    public function input(): InputInterface
    {
        return $this->input;
    }

    public function output(): OutputInterface
    {
        return $this->output;
    }

    public function style(): StyleInterface
    {
        return $this->style;
    }

    public function command(): CommandContract
    {
        return $this->command;
    }

    public function inputString(): string
    {
        return $this->input->__toString();
    }

    public function isBuilding(): bool
    {
        return 'build' == $this->commandString;
    }

    public function bind(BuilderContract $builder): bool
    {
        if (php_sapi_name() == 'cli') {
            $this->builder = $builder;

            return true;
        }

        return false;
    }

    public function run()
    {
        $exitCode = $this->runner();
        if (0 !== $exitCode) {
            exit($exitCode);
        }
        if (!isset($this->command)) {
            throw new RuntimeException(
                (new Message('No %className% command is defined'))
                    ->code('%className%', CommandContract::class)
                    ->toString()
            );
        }

        if ($this->builder == null) {
            throw new RuntimeException(
                (new Message('No Chevere %className% instance is defined'))
                    ->code('%className%', BuilderContract::class)
                    ->toString()
            );
        }

        exit($this->command->callback($this->builder));
    }

    private function addCommands(): void
    {
        $commands = [
            (new BuildCommand($this)),
            (new ClearLogsCommand($this)),
            (new RequestCommand($this)),
            (new RunCommand($this)),
            (new InspectCommand($this)),
            (new DestroyCommand($this)),
        ];
        $commandsIndex = [];
        foreach ($commands as $key => $command) {
            $this->symfony->add($command->symfony());
            $commandsIndex[$command::NAME] = $key;
        }
        $commandIndex = $commandsIndex[$this->commandString] ?? null;
        if (!isset($commandIndex)) {
            $this->style->writeln(sprintf('Command "%s" is not defined', $this->commandString));
            die(127);
        }
        $this->command = $commands[$commandIndex];
    }

    /**
     * Runs the current command.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    private function runner(): int
    {
        return $this->symfony->run(
            $this->input,
            $this->output
        );
    }
}
