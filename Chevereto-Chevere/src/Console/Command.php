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
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Console\CliContract;
use Chevere\Contracts\Console\CommandContract;

class Command extends ConsoleCommand implements CommandContract
{
    const ARGUMENT_REQUIRED = InputArgument::REQUIRED;
    const ARGUMENT_OPTIONAL = InputArgument::OPTIONAL;
    const ARGUMENT_IS_ARRAY = InputArgument::IS_ARRAY;

    const OPTION_NONE = InputOption::VALUE_NONE;
    const OPTION_REQUIRED = InputOption::VALUE_REQUIRED;
    const OPTION_OPTIONAL = InputOption::VALUE_OPTIONAL;
    const OPTION_IS_ARRAY = InputOption::VALUE_IS_ARRAY;

    /** @var CliContract */
    protected $cli;

    final public function __construct(CliContract $cli)
    {
        parent::__construct();
        $this->cli = $cli;
    }

    public function callback(LoaderContract $loader)
    {
        throw new LogicException('You must override the '.__FUNCTION__.'() method in the concrete command class.');
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cli->command = $this;
    }
}
