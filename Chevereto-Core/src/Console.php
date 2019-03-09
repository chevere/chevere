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
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use Exception;

/**
 * This class provides static access to the console.
 */
class Console
{
    protected static $app;
    public static $cli;

    /**
     * Init the Console facade.
     */
    public static function init()
    {
        $cli = new Cli();
        static::$cli = $cli;
        
        $name = $cli->getName();
        $version = $cli->getVersion();

        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $logger = new Logger($name);

        $client = new ConsoleClient($name, $version);

        $cli->setLogger($logger);
        $cli->setInput($input);
        $cli->setOutput($output);
        $cli->setIo(
            new SymfonyStyle($input, $output)
        );
        $cli->setClient($client);

        $client->add(new Command\RequestCommand($cli));
        $client->add(new Command\RunCommand($cli));
        $client->add(new Command\InspectCommand($cli));
        $client->setAutoExit(false);

        $cli->runner();
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
    /**
     * Get the value of cli
     */
    public static function cli() : Cli
    {
        return static::$cli;
    }
    /**
     * Run the console command (if any).
     */
    public static function run()
    {
        $exitCode = null;
        $cli = static::cli();
        if ($cli->has('command') == false) {
            exit($exitCode);
        }
        $command = $cli->getCommand();
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
    public static function logger() : Logger
    {
        return static::$cli->getLogger();
    }
    /**
     * Get client.
     */
    public static function client() : ConsoleClient
    {
        return static::$cli->getClient();
    }
    /**
     * Get input.
     */
    public static function input() : Input
    {
        return static::$cli->getInput();
    }
    /**
     * Get input string.
     */
    public static function inputString() : string
    {
        return (string) static::$cli->getInput();
    }
    /**
     * Get output.
     */
    public static function output() : ConsoleOutput
    {
        return static::$cli->getOutput();
    }
    /**
     * Get IO.
     */
    public static function io() : SymfonyStyle
    {
        return static::$cli->getIo();
    }
    /**
     * Detects if console context exists.
     */
    public static function exists() : bool
    {
        return isset(static::$cli);
    }
    // TODO: Fast methods to write to console (context aware so we don't neet to call ::exit)
}
// Automatic init this class in CLI
if (php_sapi_name() == 'cli') {
    Console::init();
}
