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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\ConsoleContract;
use Chevere\Contracts\Console\SymfonyCommandContract;

/**
 * Wrapper for Symfony\Component\Console\Command\Command
 */
final class SymfonyCommand extends BaseCommand implements SymfonyCommandContract
{
    /** @var CommandContract */
    private $chevereCommand;

    public function __construct(CommandContract $chevereCommand)
    {
        parent::__construct();
        $this->chevereCommand = $chevereCommand;
    }

    public function getChevereCommand(): CommandContract
    {
        return $this->chevereCommand;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }
}
