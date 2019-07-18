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

namespace Chevereto\Chevere;

use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * This class provides console for Chevereto\Chevere and it is a facade of Symfony\Component\Console.
 */
class Cli
{
    const NAME = __NAMESPACE__.' cli';
    const VERSION = '1.0';

    /** @var string Cli name */
    protected $name;

    /** @var string Cli version */
    protected $version;

    /** @var ArgvInput */
    protected $input;

    /** @var ConsoleOutput */
    protected $output;

    /** @var Logger */
    protected $logger;

    /** @var Application */
    protected $client;

    /** @var SymfonyStyle */
    protected $io;

    /** @var Command */
    protected $command;

    public function __construct(ArgvInput $input)
    {
        $this->name = static::NAME;
        $this->version = static::VERSION;
        $output = new ConsoleOutput();
        $client = new Application($this->name, $this->version);
        $logger = new Logger($this->name);

        $this->setInput($input);
        $this->setOutput($output);
        $this->setClient($client);
        $this->setLogger($logger);
        $this->setIo(
            new SymfonyStyle($input, $output)
        );
        $client->add(new Command\RequestCommand($this));
        $client->add(new Command\RunCommand($this));
        $client->add(new Command\InspectCommand($this));
        $client->setAutoExit(false);
    }

    /**
     * Run the CLI client.
     */
    public function runner()
    {
        $this->client->run($this->input, $this->output);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setInput(ArgvInput $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): ArgvInput
    {
        return $this->input;
    }

    public function setOutput(ConsoleOutput $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput(): ConsoleOutput
    {
        return $this->output;
    }

    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function setClient(Application $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getClient(): Application
    {
        return $this->client;
    }

    public function setIo(SymfonyStyle $io): self
    {
        $this->io = $io;

        return $this;
    }

    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    public function setCommand(Command $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command ?? null;
    }
}
