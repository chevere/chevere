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

use Exception;
use RuntimeException;
use Chevere\Console\Commands\BuildCommand;
use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chevere\Console\Commands\RequestCommand;
use Chevere\Console\Commands\RunCommand;
use Chevere\Console\Commands\InspectCommand;
use Chevere\Contracts\Console\CliContract;
use Chevere\Contracts\Console\CommandContract;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * This class provides console facade for Symfony\Component\Console.
 */
final class Cli implements CliContract
{
    const NAME = __NAMESPACE__.' cli';
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
    private $out;

    /** @var CommandContract */
    private $command;

    public function __construct(ArgvInput $input)
    {
        $this->input = $input;
        $this->name = static::NAME;
        $this->version = static::VERSION;
        $this->output = new ConsoleOutput();
        $this->client = new Application($this->name, $this->version);
        $this->client->setAutoExit(false);
        $this->logger = new Logger($this->name);
        $this->out = new SymfonyStyle($this->input, $this->output);

        $this->client->addCommands([
            (new RequestCommand($this))->symfonyCommand(),
            (new RunCommand($this))->symfonyCommand(),
            (new InspectCommand($this))->symfonyCommand(),
            (new BuildCommand($this))->symfonyCommand(),
        ]);
        $command = Console::command();
        try {
            $this->client->get($command);
        } catch (CommandNotFoundException $e) {
            // Shhhh, let Fabien's handle this...
        }
    }

    public function input(): ArgvInput
    {
        return $this->input;
    }

    public function out(): SymfonyStyle
    {
        return $this->out;
    }

    public function output(): ConsoleOutput
    {
        return $this->output;
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function setCommand(CommandContract $command)
    {
        $this->command =  $command;
    }

    public function command(): CommandContract
    {
        // if (!isset($this->command)) {
        //     throw new RuntimeException('Command not found');
        // }
        return $this->command;
    }

    /**
     * Runs the current command.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    public function runner(): int
    {
        return $this->client->run($this->input, $this->output);
    }
}
