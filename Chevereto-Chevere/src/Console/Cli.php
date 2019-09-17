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

namespace Chevere\Console;

use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chevere\Console\Commands\BuildCommand;
use Chevere\Console\Commands\ClearCommand;
use Chevere\Console\Commands\ClearLogsCommand;
use Chevere\Console\Commands\DestroyCommand;
use Chevere\Console\Commands\RequestCommand;
use Chevere\Console\Commands\RunCommand;
use Chevere\Console\Commands\InspectCommand;
use Chevere\Console\Commands\SetopCommand;
use Chevere\Contracts\Console\CliContract;
use Chevere\Contracts\Console\CommandContract;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use TypeError;

/**
 * This class provides Chevere CLI.
 */
final class Cli implements CliContract
{
    const NAME = __NAMESPACE__ . ' cli';
    const VERSION = '1.0';

    /** @var string Cli name */
    private $name;

    /** @var string Cli version */
    private $version;

    /** @var ArgvInput */
    private $input;

    /** @var ConsoleOutput */
    private $output;

    /** @var Logger */
    private $logger;

    /** @var Application */
    private $client;

    /** @var SymfonyStyle */
    private $style;

    /** @var CommandContract */
    private $command;

    public function __construct(ArgvInput $input, ConsoleOutput $output)
    {
        $this->input = $input;
        $this->name = static::NAME;
        $this->version = static::VERSION;
        $this->output = $output;
        $this->client = new Application($this->name, $this->version);
        $this->client->setAutoExit(false);
        $this->logger = new Logger($this->name);
        $this->style = new SymfonyStyle($this->input, $this->output);

        $this->client->addCommands([
            (new BuildCommand($this))->symfony(),
            (new ClearLogsCommand($this))->symfony(),
            (new RequestCommand($this))->symfony(),
            (new RunCommand($this))->symfony(),
            (new InspectCommand($this))->symfony(),
            (new DestroyCommand($this))->symfony(),
            // (new SetopCommand($this))->symfony(),
        ]);

        try {
            $command = Console::command();
        } catch (TypeError $e) {
            $command = 'list';
        }
        try {
            $this->client->get($command);
        } catch (CommandNotFoundException $e) {
            $this->style->writeln(sprintf('Command "%s" is not defined', $command));
            die(127);
        }
    }

    public function input(): ArgvInput
    {
        return $this->input;
    }

    public function style(): SymfonyStyle
    {
        return $this->style;
    }

    public function output(): ConsoleOutput
    {
        return $this->output;
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function setCommand(CommandContract $command): void
    {
        $this->command =  $command;
    }

    public function command(): CommandContract
    {
        return $this->command;
    }

    /**
     * {@inheritdoc}
     */
    public function runner(): int
    {
        return $this->client->run($this->input, $this->output);
    }
}
