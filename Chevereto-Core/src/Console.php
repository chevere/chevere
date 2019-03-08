<?php declare(strict_types=1);
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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use Exception;

/**
 * This class provides a facade for Monolog\Logger and Symfony\Component\Console.
 */
class Console
{
    protected static $app;
    protected static $name = 'console';
    protected static $version = '1.0';
    protected static $logger;
    protected static $client;
    protected static $input;
    protected static $output;
    protected static $io;
    protected static $command;
    protected static $bag;

    /**
     * Initializates the Console facade.
     *
     * Use only if you want to debug the console.
     *
     * @param InputInterface $input A Symfony\Component\Console\Input\InputInterface.
     * @param ConsoleOutput $output Symfony Console output.
     * @param Logger $logger Logger.
     */
    public static function init(InputInterface $input = null, ConsoleOutput $output = null, Logger $logger = null)
    {
        static::$logger = $logger ?? new Logger(static::$name);
        static::$client = new ConsoleClient(static::$name, static::$version);
        static::$input = $input ?? new ArgvInput();
        static::$output = $output ?? new ConsoleOutput();
        static::$io = new SymfonyStyle(static::$input, static::$output);
        static::client()->add(new Command\RequestCommand(static::$logger));
        static::client()->add(new Command\RunCommand(static::$logger));
        static::client()->add(new Command\InspectCommand(static::$logger));
        static::client()->setAutoExit(false);
        static::client()->run(static::$input, static::$output);
    }
    /**
     * Sets the app which interacts with this Console.
     *
     * @param App $app Chevereto\Core Application.
     *
     * @return bool TRUE if Console binds to $app (cli).
     */
    public static function setApp(App $app) : bool
    {
        if (php_sapi_name() == 'cli') {
            static::$app = $app;
            return true;
        }
        return false;
    }
    /**
     * Provides access to the currently binded $app.
     */
    public static function getApp() : ?App
    {
        return static::$app;
    }
    public static function setCommand(Command $command)
    {
        static::$command = $command;
    }
    public static function getCommand() : ?Command
    {
        return static::$command;
    }
    /**
     * Run the current console command.
     */
    public static function run()
    {
        $exitCode = null;
        $command = static::getCommand();
        if ($command instanceof Command && method_exists($command, 'callback')) {
            $app = static::getApp();
            if ($app == null) {
                throw new Exception('No app instance is defined.');
            }
            $exitCode = $command->callback($app);
        }
        exit($exitCode);
    }
    public static function logger() : Logger
    {
        return static::$logger;
    }
    public static function client() : ConsoleClient
    {
        return static::$client;
    }
    public static function input() : ArgvInput
    {
        return static::$input;
    }
    public static function getInputString() : ?string
    {
        return (string) static::$input;
    }
    public static function output() : ConsoleOutput
    {
        return static::$output;
    }
    public static function io() : SymfonyStyle
    {
        return static::$io;
    }
    public static function exists() : bool
    {
        return isset(static::$io);
    }
}
// Automatic init this class in CLI
if (php_sapi_name() == 'cli') {
    (function () {
        static::init();
    })->bindTo(null, Console::class)();
}