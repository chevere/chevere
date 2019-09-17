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
use Chevere\Console\Console;

interface ApplicationContract
{
    public function __construct(Console $console);

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
