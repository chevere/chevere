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
use Chevere\Console\Commands\RequestCommand;
use Chevere\Console\Commands\RunCommand;
use Chevere\Console\Commands\InspectCommand;

/**
 * This class provides console facade for Symfony\Component\Console.
 */
final class Cli
{
    const NAME = __NAMESPACE__.' cli';
    const VERSION = '1.0';

    /** @var string Cli name */
    public $name;

    /** @var string Cli version */
    public $version;

    /** @var ArgvInput */
    public $input;

    /** @var ConsoleOutput */
    public $output;

    /** @var Logger */
    public $logger;

    /** @var Application */
    public $client;

    /** @var SymfonyStyle */
    public $out;

    /** @var Command */
    public $command;

    public function __construct(ArgvInput $input)
    {
        $this->input = $input;
        $this->name = static::NAME;
        $this->version = static::VERSION;
        $this->output = new ConsoleOutput();
        $this->client = new Application($this->name, $this->version);
        $this->logger = new Logger($this->name);
        $this->out = new SymfonyStyle($this->input, $this->output);
        $this->client->add(new RequestCommand($this));
        $this->client->add(new RunCommand($this));
        $this->client->add(new InspectCommand($this));
        $this->client->setAutoExit(false);
    }

    public function runner()
    {
        $this->client->run($this->input, $this->output);
    }
}
