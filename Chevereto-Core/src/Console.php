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
    const OBJECTS = ['app', 'logger', 'client', 'input', 'output', 'io', 'command'];

    protected static $app;
    protected static $name = __NAMESPACE__ . ' console';
    protected static $version = '1.0';
    protected static $logger;
    protected static $client;
    protected static $input;
    protected static $output;
    protected static $io;
    protected static $command;

    /**
     * Detects if an object exists in the instance (static dependency injection).
     */
    public static function has(string $id) : bool
    {
        if (in_array($id, static::OBJECTS) == false) {
            throw new CoreException(
                (new Message("The object %s isn't handled by this class."))
                    ->code('%s', $id)
            );
        }
        return isset(static::$$id);
    }
    /**
     * Init the Console facade.
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
        static::$io = new SymfonyStyle(static::input(), static::$output);
        static::client()->add(new Command\RequestCommand(static::$logger));
        static::client()->add(new Command\RunCommand(static::$logger));
        static::client()->add(new Command\InspectCommand(static::$logger));
        static::client()->setAutoExit(false);
        static::client()->run(static::input(), static::$output);
    }
    /**
     * Binds the App which interacts with this Console.
     *
     * @param App $app Chevereto\Core Application.
     *
     * @return bool TRUE if Console binds to an App.
     */
    public static function bind(App $app) : bool
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
    public static function getApp() : App
    {
        return static::$app;
    }
    public static function setCommand(Command $command)
    {
        static::$command = $command;
    }
    public static function getCommand() : Command
    {
        return static::$command;
    }
    /**
     * Run the console command (if any).
     */
    public static function run()
    {
        $exitCode = null;
        if (static::has('command') == false) {
            exit($exitCode);
        }
        $command = static::getCommand();
        if (method_exists($command, 'callback')) {
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
    public static function getInputString() : string
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
        return isset(static::$client);
    }
}
// Automatic init this class in CLI
if (php_sapi_name() == 'cli') {
    Console::init();
}
