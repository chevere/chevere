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

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Chevere\Contracts\Console\CliContract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\BaseCommandContract;

final class BaseCommand extends SymfonyCommand implements BaseCommandContract
{
    /** @var CliContract */
    protected $cli;

    /** @var CommandContract */
    protected $command;

    public function __construct(CliContract $cli)
    {
        parent::__construct();
        $this->cli = $cli;
    }

    public function setCommand(CommandContract $command)
    {
        $this->command = $command;
        $this->command->configure();
    }

    public function command(): CommandContract
    {
        return $this->command;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cli->command = $this->command;
    }
}
