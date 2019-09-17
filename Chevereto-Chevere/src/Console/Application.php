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

use Chevere\Console\Commands\SetopCommand;
use Chevere\Contracts\Console\ApplicationContract;
use Chevere\Contracts\Console\CommandContract;
use TypeError;

/**
 * This class provides Chevere console application.
 */
final class Application implements ApplicationContract
{
    /** @var Console */
    private $console;

    /** @var CommandContract */
    private $command;

    public function __construct(Console $console)
    { }

    public function console(): Console
    {
        return $this->console;
    }

    public function setCommand(CommandContract $command): void
    {
        $this->command =  $command;
    }

    public function command(): CommandContract
    {
        return $this->command;
    }
}
