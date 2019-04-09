<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Monolog\Logger;
use Symfony\Component\Console\Application as ConsoleClient;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Exception;

class Console
{
    const VERBOSITY_QUIET = ConsoleOutput::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = ConsoleOutput::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = ConsoleOutput::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = ConsoleOutput::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = ConsoleOutput::VERBOSITY_DEBUG;

    const OUTPUT_NORMAL = ConsoleOutput::OUTPUT_NORMAL;
    const OUTPUT_RAW = ConsoleOutput::OUTPUT_RAW;
    const OUTPUT_PLAIN = ConsoleOutput::OUTPUT_PLAIN;

    protected static $app;
    protected static $cli;
    protected static $available;

    /**
     * Init the Console facade.
     */
    public static function init()
    {
        $cli = new Cli(new ArgvInput());
        static::$cli = $cli;
        static::$available = true;
    }

    /**
     * Binds the App which interacts with this Console.
     *
     * @param App $app chevereto\Core Application
     *
     * @return bool TRUE if Console binds to an App
     */
    public static function bind(App $app): bool
    {
        if (php_sapi_name() == 'cli') {
            static::setApp($app);

            return true;
        }

        return false;
    }

    public static function setApp(App $app)
    {
        static::$app = $app;
    }

    public static function getApp(): App
    {
        return static::$app;
    }

    /**
     * Get the value of cli.
     */
    public static function cli(): Cli
    {
        return static::$cli;
    }

    /**
     * Run the console command (if any).
     */
    public static function run()
    {
        $cli = static::cli();
        $exitCode = $cli->runner();
        $command = $cli->getCommand();
        if (null === $command) {
            exit($exitCode);
        }
        if (method_exists($command, 'callback')) {
            $app = static::getApp();
            if ($app == null) {
                throw new Exception('No app instance is defined.');
            }
            $exitCode = $command->callback($app);
        }
        exit($exitCode);
    }

    /**
     * Get logger.
     */
    public static function logger(): Logger
    {
        return static::cli()->getLogger();
    }

    /**
     * Get client.
     */
    public static function client(): ConsoleClient
    {
        return static::cli()->getClient();
    }

    /**
     * Get input.
     */
    public static function input(): ArgvInput
    {
        return static::cli()->getInput();
    }

    /**
     * Get input string.
     */
    public static function inputString(): string
    {
        $input = static::input();
        if (method_exists($input, '__toString')) {
            return static::input()->__toString();
        }

        return 'n/a';
    }

    /**
     * Get output.
     */
    public static function output(): ConsoleOutput
    {
        return static::cli()->getOutput();
    }

    /**
     * Get IO.
     */
    public static function io(): SymfonyStyle
    {
        return static::cli()->getIo();
    }

    /**
     * Detects if Console is available.
     */
    public static function isRunning(): bool
    {
        return (bool) static::$available;
    }

    /**
     * Write messages to the console.
     *
     * @param string|array $messages the message as an iterable of strings or a single string
     * @param int          $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function write($messages, int $options = self::OUTPUT_NORMAL): void
    {
        if (!static::isRunning()) {
            return;
        }
        static::io()->write($messages, false, $options);
    }

    /**
     * Write messages (new lines) to the console.
     *
     * @param string|array $messages the message as an iterable of strings or a single string
     * @param int          $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public static function writeln($messages, int $options = self::OUTPUT_NORMAL): void
    {
        if (!static::isRunning()) {
            return;
        }
        static::io()->writeln($messages, $options);
    }

    public static function log($messages)
    {
        if (!static::isRunning()) {
            return;
        }
        static::io()->writeln($messages);
    }
}
