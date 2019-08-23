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

namespace Chevere\Contracts\Console;

use Exception;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

interface CliContract
{
    public function __construct(ArgvInput $input);

    public function input(): ArgvInput;

    public function out(): SymfonyStyle;

    public function output(): ConsoleOutput;

    public function logger(): Logger;

    public function setCommand(CommandContract $command): void;

    public function command(): CommandContract;

    /**
     * Runs the current command.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    public function runner(): int;
}
