<?php

// Deprecate Static
declare(strict_types=1);

namespace Chevereto\Core;

use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * VERBOSE LEVELS
 * -q: Quiet (no imprime nada, solo exit(code))
 * <null>: (imprime 0-4)
 * -v: Increaded verbosity (imprime 0-5)
 * -vv: (imprime 0-6)
 * -vvv: (imprime 0-7).
 */

/**
 * SEVERITY LEVELS.
 *
 * 0: Emergency
 * 1: Alert
 * 2: Critical
 * 3: Error
 * 4: Warning
 * 5. Notice
 * 6. Informational
 * 7: Debug
 */

/**
 * Log provides static global log helpers for the whole app, with CLI support.
 *
 * Log::debug(<var>,...);
 * Log::channel(<channel>)->error();
 *
 * Placeholders:
 * %m: Method
 * %f: File
 * %L: Line
 * %F: File:Line
 *
 * Unable to parse username in app/lib/pico.php:123
 */
class Log
{
    const SEVERITY_LEVELS = [
        0 => 'emergency',
        1 => 'alert',
        2 => 'critical',
        3 => 'error',
        4 => 'warning',
        5 => 'notice',
        6 => 'info',
        7 => 'debug',
    ];
    const VERBOSITY_MAP = [
        OutputInterface::VERBOSITY_QUIET => [], // -q no messages
        OutputInterface::VERBOSITY_NORMAL => [0, 1, 2, 3, 4], // <null> emergency-warning
        OutputInterface::VERBOSITY_VERBOSE => [0, 1, 2, 3, 4, 5], // -v emergency-notice
        OutputInterface::VERBOSITY_VERY_VERBOSE => [0, 1, 2, 3, 4, 5, 6], // -vv emergency-info
        OutputInterface::VERBOSITY_DEBUG => [0, 1, 2, 3, 4, 5, 6, 7], // -vvv emergency-debug
    ];
    protected static $loggerContainer = [];
    protected static $verboseSet = [];
    protected static $useConsole = false;

    /**
     * Creates Log object.
     */
    protected static function init()
    {
        if (php_sapi_name() == 'cli') {
            static::$useConsole = true;
        }
        static::$loggerContainer['app'] = new Logger('app');
        $verbosity = static::$useConsole ? Console::output()->getVerbosity() : OutputInterface::VERBOSITY_NORMAL;
        // Set array levelName => int to handle levels
        static::$verboseSet = static::VERBOSITY_MAP[$verbosity];
        static::$verboseSet = Utils\Arr::filterArray(static::SEVERITY_LEVELS, static::$verboseSet);
        static::$verboseSet = array_flip(static::$verboseSet);
    }

    /**
     * Allows to set the target log channel.
     *  (slack, files, email, etc).
     */
    // public static function channel(string $channelName)
    // {
    // }

    protected static function isLevelBeingUsed(string $levelName)
    {
        return isset(static::$verboseSet[$levelName]);
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * Emergency messages indicate that the system is unusable. A panic condition.
     *
     * TODO: Notify contacts
     *
     * Examples:
     * - "The datacenter is on fire."
     * - "The whole building is inside the eye of the tornado."
     *
     * @param string $message The log message
     */
    public static function emergency(string $message)
    {
        dump($message, 'emergency');
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * Alert messages indicate that action must be taken immediately.
     *
     * TODO: Notify contacts
     *
     * Examples:
     * - "Unable to connect to DB server."
     * - "Class <class> doesn't exists."
     *
     * @param string $message The log message
     */
    public static function alert(string $message)
    {
        dump($message, 'alert');
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * Critical messages indicate conditions that should be corrected immediately, and also indicate failure in a
     * secondary system.
     *
     * Examples:
     * - "MySQL server <server> gone."
     * - "Unrecoverable DB error: <error>"
     *
     * @param string $message The log message
     */
    public static function critical(string $message)
    {
        dump($message, 'critical');
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * Error messages indicate non-urgent failures.
     *
     * Examples:
     * - ""
     *
     * @param string $message The log message
     */
    public static function error(string $message)
    {
        dump($message, 'error');
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * Warning messages indicate that that an error will occur if action is not taken.
     *
     * Examples:
     * - "Filesystem is 90% full."
     * - "Method <method> is deprecated in %f:%l. Migrate to <newMethod> to avoid an ALERT."
     *
     * @param string $message The log message
     */
    public static function warning(string $message)
    {
        dump($message, 'warning');
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * Notice messages indicate events that are unusual but that are not error conditions. They can be used to spot potential problems, but no immediate action is necessary.
     *
     * Examples:
     * - "Could not load configuration file from <path>. Using defaults."
     * - "External storage server <server> is unrecheable. Switching to local storage."
     * - "Username <username> already exists. Trying to create user <usernameAlt>..."
     *
     * @param string $message The log message
     */
    public static function notice(string $message)
    {
        // dump('Notice called :', $message);
        if (static::isLevelBeingUsed('notice')) {
            // dump($message);
            Console::logger()->critical($message);
        }
    }

    /**
     * TODO: Default level?
     * Adds a log record at the INFO level.
     *
     * Informational messages are associated with normal operational behavior. They may be tracked for reporting, measuring throughput, or other purposes, but no action is required.
     *
     * Examples:
     * - "Router initialized."
     * - "Showing user profile for user <id>."
     *
     * @param string $message The log message
     */
    public static function info(string $message)
    {
        if (static::isLevelBeingUsed(__FUNCTION__)) {
            dump($message);
        }
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * Debug messages are useful to developers for debugging the application, but are not useful for tracking operations.
     *
     * Examples:
     * - "
     *
     * @param string $message The log message
     */
    public static function debug(string $message)
    {
        dump($message, 'debug');
    }
}
