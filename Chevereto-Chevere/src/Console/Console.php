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

use Chevere\App\Loader;
use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Console\ConsoleContract;
use Chevere\Contracts\Console\CliContract;

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

    /** @var LoaderContract */
    private static $loader;

    /** @var CliContract */
    private static $cli;

    /** @var bool */
    private static $available;

    /** @var string The first argument (command) passed */
    private static $command;

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
        $input = new ArgvInput();
        self::$command = $input->getFirstArgument();
        self::$cli = new Cli($input);
        self::$available = true;
    }

    public static function isBuilding(): bool
    {
        return self::isRunning() && 'build' == self::$command;
    }

    public static function cli(): CliContract
    {
        return self::$cli;
    }

    public static function run()
    {
        if (!self::isRunning()) {
            return;
        }
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

    public static function write(string $message, int $options = Console::OUTPUT_NORMAL): void
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->write($message, false, $options);
    }

    public static function writeln(string $message, int $options = Console::OUTPUT_NORMAL): void
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->writeln($message, $options);
    }

    public static function log(string $message)
    {
        if (!self::isRunning()) {
            return;
        }
        self::$cli->out->writeln($message);
    }
}
