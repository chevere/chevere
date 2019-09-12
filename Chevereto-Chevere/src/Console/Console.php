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
use Chevere\Contracts\Console\CliContract;
use Chevere\Message;
use Throwable;
use TypeError;

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

    public static function command(): string
    {
        return self::$command;
    }

    public static function isBuilding(): bool
    {
        try {
            $command = self::command();
            return self::isAvailable() && 'build' == $command;
        } catch (TypeError $e) {
            return false;
        }
    }

    public static function cli(): CliContract
    {
        return self::$cli;
    }

    public static function run()
    {
        if (!self::isAvailable()) {
            throw new RuntimeException(
                (new Message('Unable to call %method% when %class% is not available.'))
                    ->code('%method%', __METHOD__)
                    ->code('%class%', __CLASS__)
                    ->toString()
            );
        }
        $exitCode = self::$cli->runner();
        if (0 !== $exitCode) {
            die();
        }
        try {
            $command = self::$cli->command();
        } catch (Throwable $e) {
            exit($exitCode);
        }
        if (self::$loader == null) {
            throw new RuntimeException('No Chevere instance is defined.');
        }
        exit($command->callback(self::$loader));
    }

    public static function inputString(): string
    {
        if (method_exists(self::$cli->input(), '__toString')) {
            return self::$cli->input()->__toString();
        }

        return '';
    }

    public static function isAvailable(): bool
    {
        return (bool) self::$available;
    }

    public static function write(string $message, int $options = Console::OUTPUT_NORMAL): void
    {
        self::$cli->style()->write($message, false, $options);
    }

    public static function writeln(string $message, int $options = Console::OUTPUT_NORMAL): void
    {
        self::$cli->style()->writeln($message, $options);
    }
}
