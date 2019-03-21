<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Monolog\Logger;
use Symfony\Component\Console\Application as ConsoleClient;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use Exception;

/**
 * This class provides console for Chevereto\Core and it is a facade of Symfony\Component\Console.
 */
class Cli
{
    const OBJECTS = ['logger', 'client', 'input', 'output', 'io', 'command'];

    const NAME = __NAMESPACE__ . ' cli';
    const VERSION = '1.0';

    protected $name;
    protected $version;
    protected $input;
    protected $output;
    protected $logger;
    protected $client;
    protected $io;
    protected $command;

    // TODO: Inject logger?
    public function __construct(ArgvInput $input)
    {
        $this->name = static::NAME;
        $this->version = static::VERSION;
        $output = new ConsoleOutput();
        $client = new ConsoleClient($this->name, $this->version);
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
     * Detects if an object exists in the instance.
     */
    public function has(string $id) : bool
    {
        if (in_array($id, static::OBJECTS) == false) {
            throw new CoreException(
                (new Message("The object %s isn't handled by %c."))
                    ->code('%s', $id)
                    ->code('%c', __CLASS__)
            );
        }
        return isset($this->{$id});
    }
    /**
     * Run the CLI client.
     */
    public function runner()
    {
        $this->client->run($this->input, $this->output);
    }
    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Set the value of version.
     *
     * @return  self
     */
    public function setVersion(string $version) : self
    {
        $this->version = $version;
        return $this;
    }
    /**
     * Set the value of input.
     *
     * @return  self
     */
    public function setInput(ArgvInput $input) : self
    {
        $this->input = $input;
        return $this;
    }
    /**
     * Set the value of output.
     *
     * @return  self
     */
    public function setOutput(ConsoleOutput $output) : self
    {
        $this->output = $output;
        return $this;
    }
    /**
     * Set the value of logger.
     *
     * @return  self
     */
    public function setLogger(Logger $logger) : self
    {
        $this->logger = $logger;
        return $this;
    }
    /**
     * Set the value of client.
     *
     * @return  self
     */
    public function setClient(ConsoleClient $client) : self
    {
        $this->client = $client;
        return $this;
    }
    /**
     * Set the value of io.
     *
     * @return  self
     */
    public function setIo(SymfonyStyle $io) : self
    {
        $this->io = $io;
        return $this;
    }
    /**
     * Set the value of command.
     *
     * @return  self
     */
    public function setCommand(Command $command) : self
    {
        $this->command = $command;
        return $this;
    }
    /**
     * Get the value of name.
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * Get the value of version
     */
    public function getVersion() : string
    {
        return $this->version;
    }
    /**
     * Get the value of input.
     *
     * @return ArgvInput
     */
    public function getInput() : ArgvInput
    {
        return $this->input;
    }
    /**
     * Get the value of output.
     */
    public function getOutput() : ConsoleOutput
    {
        return $this->output;
    }
    /**
     * Get the value of logger.
     */
    public function getLogger() : Logger
    {
        return $this->logger;
    }
    /**
     * Get the value of client.
     */
    public function getClient() : ConsoleClient
    {
        return $this->client;
    }
    /**
     * Get the value of io.
     */
    public function getIo() : SymfonyStyle
    {
        return $this->io;
    }
    /**
     * Get the value of command.
     */
    public function getCommand() : Command
    {
        return $this->command;
    }
}
