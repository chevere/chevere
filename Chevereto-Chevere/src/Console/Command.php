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

use LogicException;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\App\App;
use Chevere\Interfaces\CommandInterface;

class Command extends ConsoleCommand implements CommandInterface
{
    /**
     * Provide input arguments contants shortcuts.
     */
    const ARGUMENT_REQUIRED = InputArgument::REQUIRED;
    const ARGUMENT_OPTIONAL = InputArgument::OPTIONAL;
    const ARGUMENT_IS_ARRAY = InputArgument::IS_ARRAY;

    /**
     * Provide input options contants shortcuts.
     */
    const OPTION_NONE = InputOption::VALUE_NONE;
    const OPTION_REQUIRED = InputOption::VALUE_REQUIRED;
    const OPTION_OPTIONAL = InputOption::VALUE_OPTIONAL;
    const OPTION_IS_ARRAY = InputOption::VALUE_IS_ARRAY;

    /** @var Cli */
    public $cli;

    public function __construct(Cli $cli)
    {
        // $this->logger = $logger;
        $this->cli = $cli;
        parent::__construct();
    }

    /**
     * Sets the Cli command to execute. Used internally by Symfony.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cli->command = $this;
    }

    /**
     * Callback contains the actual command in-app instructions.
     */
    public function callback(App $app)
    {
        throw new LogicException('You must override the ' . __FUNCTION__ . '() method in the concrete command class.');
    }
}
