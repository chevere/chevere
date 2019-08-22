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
 * As this class extends, this wrapper allows to add functionality without causing naming collisions.
 */
final class SymfonyCommand extends BaseCommand implements SymfonyCommandContract
{
    /** @var CliContract */
    protected $chevereCli;

    /** @var CommandContract */
    protected $chevereCommand;

    public function __construct(CliContract $cli)
    {
        $this->chevereCli = $cli;
        parent::__construct();
    }

    public function chevereSetCommand(CommandContract $command)
    {
        $this->chevereCommand = $command;
        $this->chevereCommand->configure();
    }

    public function chevereCommand(): CommandContract
    {
        return $this->chevereCommand;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->chevereCli->command = $this->chevereCommand;
    }
}
