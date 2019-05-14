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

namespace Chevereto\Chevere\ErrorHandler;

use const Chevereto\Chevere\ROOT_PATH;
use const Chevereto\Chevere\App\PATH;
use Chevereto\Chevere\App;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Utils\DateTime;
use ErrorException;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Super flat and minimal error handler utility.
 *
 * - Handles PHP errors as exceptions
 * - Provides clean and usable messages in HTML/markdown format
 * - Logs to system using Monolog
 * - Logs on a daily basis or any alternative datetime format you want to
 * - Uses UTC for everything
 * - Configurable debug output (app/config.php)
 * - CLI channel
 *
 * Psst, edit the constants when extending.
 */
class ErrorHandler extends Processor
{
    // Customize the relative folder where logs will be stored
    const LOG_DATE_FOLDER = 'Y/m/d/';
    // null will read app/config.php. Any boolean value will override that
    const DEBUG = null;
    // null will use App\PATH_LOGS ? PATH_LOGS ? traverse
    const PATH_LOGS = ROOT_PATH . App\PATH . 'var/logs/';
    // Title with debug = false
    const NO_DEBUG_TITLE = 'Something went wrong';
    // Content with debug = false
    const NO_DEBUG_CONTENT = '<p>The system has failed and the server wasn\'t able to fulfil your request. This incident has been logged.</p><p>Please try again later and if the problem persist don\'t hesitate to contact your system administrator.</p>';
    // CSS stylesheet
    const CSS = 'html{color:#000;font:16px Helvetica,Arial,sans-serif;line-height:1.3;background:#3498db;background:-moz-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:-webkit-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:linear-gradient(to bottom,#3498db 0%,#8e44ad 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#3498db",endColorstr="#8e44ad",GradientType=0)}.body--block{margin:20px}.body--flex{margin:0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.user-select-none{-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}main{background:none;display:block;padding:0;margin:0;border:0;width:470px}.body--block main{margin:0 auto}@media (min-width:768px){main{padding:20px}}.main--stack{width:100%;max-width:900px}.hr{display:block;height:1px;color:transparent;background:hsl(192,15%,84%)}.hr>span{opacity:0;line-height:0}.main--stack hr:last-of-type{margin-bottom:0}.t{font-weight:700;margin-bottom:5px}.t--scream{font-size:2.25em;margin-bottom:0}.t--scream span{font-size:.667em;font-weight:400}.t--scream span::before{white-space:pre;content:"\A"}.t>.hide{display:inline-block}.c code{padding:2px 5px}.c code,.c pre{background:hsl(192,15%,95%);line-height:normal}.c pre.pre--even{background:hsl(192,15%,97%)}.c pre{overflow:auto;word-wrap:break-word;font-size:13px;font-family:Consolas,monospace,sans-serif;display:block;margin:0;padding:10px}main>div{padding:20px;background:#FFF}main>div,main>div> *{word-break:break-word;white-space:normal}@media (min-width:768px){main>div{-webkit-box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);border-radius:2px}}main>div>:first-child{margin-top:0}main>div>:last-child{margin-bottom:0}.note{margin:1em 0}.fine-print{color:#BBB}.hide{width:0;height:0;opacity:0;overflow:hidden}
    .c pre {
        border: 1px solid hsl(192,15%,84%);
        border-bottom: 0; 
        border-top: 0;
    }';
    /**
     * Stack template.
     *
     * HTML template for each stack entry
     *
     * Available placeholders:
     * - %x% Applies even class (pre--even)
     * - %i% Stack number
     * - %f% File
     * - %l% Line
     * - %fl% File + Line
     * - %c% class
     * - %t% type (::, ->)
     * - %m% Method (function)
     * - %a% Arguments
     */
    const HTML_STACK_TEMPLATE = "<pre class=\"%x%\">#%i% %fl%\n%c%%t%%m%()%a%</pre>";
    const CONSOLE_STACK_TEMPLATE = "#%i% %fl%\n%c%%t%%m%()%a%";
    // HTML template (document)
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="generator" content="Chevereto\Chevere"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';
    const CONSOLE_TEMPLATE = '%body%';
    // HTML body for debug = false
    const HTML_BODY_NO_DEBUG_TEMPLATE = '<main><div><div class="t t--scream">%title%</div>%content%<p class="fine-print">%datetimeUtc% • %id%</p></div></main>';
    // HTML body for debug = true
    const HTML_BODY_DEBUG_TEMPLATE = '<main class="main--stack"><div>%content%<div class="c note user-select-none"><b>Note:</b> This message is being displayed because of active debug mode. Remember to turn this off when going production by editing <code>%configFilePath%</code></div></div></main>';
    const CONFIG_FILE_PATH = App\PATH . 'config.php';
    const COLUMNS = 120;
    // Line break
    const HR = '<div class="hr"><span>------------------------------------------------------------</span></div>';
    // Section keys
    const SECTION_TITLE = 'title';
    const SECTION_MESSAGE = 'message';
    const SECTION_ID = 'id';
    const SECTION_TIME = 'time';
    const SECTION_STACK = 'stack';
    const SECTION_CLIENT = 'client';
    const SECTION_REQUEST = 'request';
    const SECTION_SERVER = 'server';

    /**
     * Verbose aware console sections.
     */
    const CONSOLE_TABLE = [
        self::SECTION_TITLE => OutputInterface::VERBOSITY_QUIET,
        self::SECTION_MESSAGE => OutputInterface::VERBOSITY_QUIET,
        self::SECTION_ID => OutputInterface::VERBOSITY_NORMAL,
        self::SECTION_TIME => OutputInterface::VERBOSITY_VERBOSE,
        self::SECTION_STACK => OutputInterface::VERBOSITY_VERY_VERBOSE,
        self::SECTION_CLIENT => OutputInterface::VERBOSITY_VERBOSE,
        self::SECTION_REQUEST => false,
        self::SECTION_SERVER => false,
    ];
    /**
     * PHP error to monolog table
     * code => [monolog code, title].
     */
    const ERROR_TABLE = [
        E_ERROR => 'Fatal error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core error',
        E_CORE_WARNING => 'Core warning',
        E_COMPILE_ERROR => 'Compile error',
        E_COMPILE_WARNING => 'Compile warning',
        E_USER_ERROR => 'Fatal error',
        E_USER_WARNING => 'Warning',
        E_USER_NOTICE => 'Notice',
        E_STRICT => 'Strict standars',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'Deprecated',
    ];
    /**
     * PHP error code LogLevel table
     * Taken from Monolog\ErrorHandler (defaultErrorLevelMap).
     */
    const PHP_LOG_LEVEL = [
        E_ERROR => LogLevel::CRITICAL,
        E_WARNING => LogLevel::WARNING,
        E_PARSE => LogLevel::ALERT,
        E_NOTICE => LogLevel::NOTICE,
        E_CORE_ERROR => LogLevel::CRITICAL,
        E_CORE_WARNING => LogLevel::WARNING,
        E_COMPILE_ERROR => LogLevel::ALERT,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::ERROR,
        E_USER_WARNING => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::NOTICE,
        E_STRICT => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED => LogLevel::NOTICE,
        E_USER_DEPRECATED => LogLevel::NOTICE,
    ];

    /**
     * Exception handler.
     *
     * Turns exceptions into printable messages, both HTML and error log.
     *
     * This method
     */
    protected static function exceptionHandler()
    {
        $that = new static();
        $that->setArguments(...func_get_args());
        $that->setXhrConditional();
        $that->setSignatureProperties();
        $that->setConstantProperties();
        $that->setServerProperties();
        $that->setDebug();
        $that->setBodyClass();
        $that->setExceptionProperties();
        $that->setLogProperties();
        $that->setLogger();
        $that->setStack();
        $that->setContentSections();
        $that->appendContentGlobals();
        $that->generateContentTemplate();
        $that->setContentProperties();
        $that->parseContentTemplate();
        $that->loggerExec();
        $that->setOutput();
        $that->out();
    }

    /**
     * Procedural-style error handler.
     *
     * Turns every PHP error into an exception, for better error traceability.
     */
    public static function error($severity, $message, $file, $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Procedural-syle exception handler.
     */
    public static function exception($e): void
    {
        static::exceptionHandler(...func_get_args());
    }
}
