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

namespace Chevere\Components\Console;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Application as Symfony;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Chevere\Components\Console\Commands\BuildCommand;
use Chevere\Components\Console\Commands\ClearLogsCommand;
use Chevere\Components\Console\Commands\DestroyCommand;
use Chevere\Components\Console\Commands\InspectCommand;
use Chevere\Components\Console\Commands\RequestCommand;
use Chevere\Components\Console\Commands\RunCommand;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\Console\Interfaces\CommandInterface;
use Chevere\Components\Console\Interfaces\ConsoleInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * The built-in Chevere application console.
 */
final class Console implements ConsoleInterface
{
    private ArgvInput $input;

    private ConsoleOutputInterface $output;

    private bool $isBuiltIn;

    private Symfony $symfony;

    private SymfonyStyle $style;

    private BuilderInterface $builder;

    private array $commandNames = [
        BuildCommand::class,
        ClearLogsCommand::class,
        RequestCommand::class,
        RunCommand::class,
        InspectCommand::class,
        DestroyCommand::class,
    ];

    private CommandInterface $command;

    /** @var string The first argument (command name) passed */
    private string $commandName;

    public function __construct()
    {
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->commandName = $this->input->getFirstArgument();
        $this->isBuiltIn = in_array($this->commandName, ['list', 'help']);
        $this->symfony = new Symfony(self::NAME, self::VERSION);
        $this->symfony->setAutoExit(false);
        $this->style = new SymfonyStyle($this->input, $this->output);
        if ('' == $this->commandName) {
            $this->style->block(
                (new Message('Command argument required'))
                    ->toString(),
                'COMMAND REQUIRED'
            );
            die(1);
        }
        $this->addCommands();
    }

    public function withCommand(CommandInterface $command): ConsoleInterface
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

    public function style(): SymfonyStyle
    {
        return $this->style;
    }

    public function command(): CommandInterface
    {
        return $this->command;
    }

    public function inputString(): string
    {
        return $this->input->__toString();
    }

    public function isBuilding(): bool
    {
        return 'build' == $this->commandName;
    }

    public function bind(BuilderInterface $builder): bool
    {
        if ('cli' == php_sapi_name()) {
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
        if ($this->isBuiltIn) {
            exit(0);
        }
        if (!isset($this->command)) {
            throw new RuntimeException(
                (new Message('No %className% command is defined'))
                    ->code('%className%', CommandInterface::class)
                    ->toString()
            );
        }

        if (null == $this->builder) {
            throw new RuntimeException(
                (new Message('No %className% instance is defined'))
                    ->code('%className%', BuilderInterface::class)
                    ->toString()
            );
        }

        exit($this->command->callback($this->builder));
    }

    private function addCommands(): void
    {
        $commands = [];
        $index = [];
        foreach ($this->commandNames as $commandName) {
            $command = new $commandName($this);
            $this->symfony->add($command->symfony());
            $commands[] = $command;
            $index[] = $command::NAME;
        }
        $pos = array_search($this->commandName, $index);
        if (false === $pos) {
            if (!$this->isBuiltIn) {
                $this->style->block(
                    (new Message('Command %command% is not defined'))
                        ->code('%command%', $this->commandName)
                        ->toString(),
                    'NOT FOUND'
                );
                die(127);
            }
        } else {
            $this->command = $commands[$pos];
        }
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
