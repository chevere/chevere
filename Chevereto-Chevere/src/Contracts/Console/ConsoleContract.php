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

use Chevere\Contracts\App\LoaderContract;

interface ConsoleContract
{
    public static function bind(LoaderContract $loader): bool;

    public static function init();

    public static function cli(): CliContract;

    public static function run();

    public static function inputString(): string;

    public static function isRunning(): bool;

    /**
     * Write messages to the console.
     *
     * @param string $message the message string
     * @param int    $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function write(string $message, int $options = self::OUTPUT_NORMAL): void;

    /**
     * Write messages (new lines) to the console.
     *
     * @param string|array $message the message string
     * @param int          $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function writeln(string $message, int $options = self::OUTPUT_NORMAL): void;

    public static function log(string $message);
}
