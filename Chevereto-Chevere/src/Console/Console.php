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

use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chevere\App\Loader;

/**
 * Provides static access to the Chevere application console.
 */
final class Console
{
    const VERBOSITY_QUIET = ConsoleOutput::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = ConsoleOutput::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = ConsoleOutput::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = ConsoleOutput::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = ConsoleOutput::VERBOSITY_DEBUG;

    const OUTPUT_NORMAL = ConsoleOutput::OUTPUT_NORMAL;
    const OUTPUT_RAW = ConsoleOutput::OUTPUT_RAW;
    const OUTPUT_PLAIN = ConsoleOutput::OUTPUT_PLAIN;

    /** @var Loader */
    private static $loader;

    /** @var Cli */
    private static $cli;

    /** @var bool */
    private static $available;

    public static function bind(Loader $loader): bool
    {
        if (php_sapi_name() == 'cli') {
            self::$loader = $loader;

            return true;
        }

        return false;
    }

    public static function init()
    {
        $cli = new Cli(new ArgvInput());
        self::$cli = $cli;
        self::$available = true;
    }

    public static function cli(): Cli
    {
        return self::$cli;
    }

    public static function run()
    {
        $exitCode = self::$cli->runner();
        $command = self::$cli->command;
        if (null === $command) {
            exit($exitCode);
        }
        if (method_exists($command, 'callback')) {
            if (self::$loader == null) {
                throw new RuntimeException('No Chevere instance is defined.');
            }
            $exitCode = $command->callback(self::$loader);
        }
        exit($exitCode);
    }

    public static function inputString(): string
    {
        if (method_exists(self::$cli->input, '__toString')) {
            return self::$cli->input->__toString();
        }

        return '';
    }

    public static function isRunning(): bool
    {
        return (bool) self::$available;
    }

    /**
     * Write messages to the console.
     *
     * @param string|array $messages the message as an iterable of strings or a single string
     * @param int          $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function write($messages, int $options = self::OUTPUT_NORMAL): void
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->write($messages, false, $options);
    }

    /**
     * Write messages (new lines) to the console.
     *
     * @param string|array $messages the message as an iterable of strings or a single string
     * @param int          $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function writeln($messages, int $options = self::OUTPUT_NORMAL): void
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->writeln($messages, $options);
    }

    public static function log($messages)
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->writeln($messages);
    }
}
