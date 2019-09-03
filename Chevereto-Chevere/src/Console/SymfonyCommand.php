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

use Symfony\Component\Console\Command\Command as BaseCommand;
use Chevere\Contracts\Console\CliContract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\SymfonyCommandContract;

/**
 * Wrapper for Symfony\Component\Console\Command\Command
 */
final class SymfonyCommand extends BaseCommand implements SymfonyCommandContract
{
    /** @var CliContract */
    private $chevereCli;

    /** @var CommandContract */
    private $chevereCommand;

    public function __construct(CommandContract $command)
    {
        $this->chevereCommand = $command;
        $this->chevereCli = $command->cli();
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->chevereCli->setCommand($this->chevereCommand);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }
}
