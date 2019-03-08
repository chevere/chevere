<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// OK
//todo
// Validar session save path (must exists)
namespace Chevereto\Core;

use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * System configuration class
 */
class Config
{
    // Config keys
    const DEBUG = 'debug';
    const TIMEZONE = 'timezone';
    const EXCEPTION_HANDLER = 'exception_handler';
    const ERROR_HANDLER = 'error_handler';
    const HTTP_SCHEME = 'http_scheme';
    // Cache modes
    const CACHE_MODE_ON = 'on';
    const CACHE_MODE_OFF = 'off';
    const CACHE_MODE_AUTO = 'auto';
    // Cache modes apply
    const ROUTER_CACHE_MODE = 'router_cache_mode';

    protected static $configFileName = App::CONFIG_FILENAME;

    /**
     * Stores the loaded file path.
     */
    protected static $loaded;
    /**
     * Stores the values passed using Config::set* methods.
     */
    protected static $values = [];
    // a. key => [values,] (allowed values)
    // b. key => callable
    protected static $asserts = [
        self::DEBUG             => [0, 1],
        self::TIMEZONE          => __NAMESPACE__ . '\Validate::timezone',
        self::EXCEPTION_HANDLER => 'is_callable',
        self::ERROR_HANDLER     => 'is_callable',
        self::HTTP_SCHEME       => ['http', 'https'],
    ];
    /**
     * Load and validate app config.
     *
     * @throws Exception.
     */
    public static function load() : void
    {
        $load = Load::app(static::$configFileName);
        static::setBulk($load);
        try {
            static::validate();
        } catch (Exception $e) {
            throw new Exception(
                (new Message($e->getMessage() . ' ' . 'at %s'))->b('%s', static::$loaded)
            );
        }
    }
    /**
     * Apply config.
     *
     * @throws ConfigException
     */
    public static function apply() : void
    {
        if (static::getValues() == null) {
            throw new Exception('Unable to apply config (no values)');
        }
        // Register these custom handlers asap so Chevereto\Core won't mess
        foreach ([static::EXCEPTION_HANDLER, static::ERROR_HANDLER] as $v) {
            if (static::has($v)) {
                $callable = static::get($v);
                // If null, restore error handler (runtime sets Chevereto's)
                if ($callable === null) {
                    $restore = 'restore_' . static::EXCEPTION_HANDLER;
                    is_callable($restore) ? $restore() : null;
                    continue;
                }
                if (is_callable(static::get($v))) {
                    $fn = 'set_' . $v;
                    is_callable($fn) ? $fn(static::get($v)) : null;
                } else {
                    throw new ConfigException($v);
                }
            }
        }
        
        /**
         * Determine the HTTP sheme based on config values, failover to $_SERVER
         * detection. This workaround is just because some don't know how to
         * bind https, meaning that $_SERVER is not a 100% reliable source for
         * scheme information.
         */
        if (static::has(static::HTTP_SCHEME)) {
            $scheme = static::get(static::HTTP_SCHEME);
        } else {
            $app = App::instance();
            $request = $app->getRequest();
            $scheme = $app->getRequest()->getScheme();
            //
            // Auto detect protocol (old way, la csm)
            // $scheme = 'http';
            // if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
            //     $scheme .= 's';
            // }
            //
            static::set(static::HTTP_SCHEME, $scheme);
        }
        if (static::has(static::TIMEZONE)) {
            date_default_timezone_set(static::get(static::TIMEZONE));
        }
    }
    /**
     * Set config property.
     *
     * @param string $key Config key.
     * @param mixed $value Config value.
     */
    public static function set(string $key, $value) : void
    {
        static::$values[$key] = $value;
    }
    /**
     * Bullk-set config values.
     *
     * @param array $values Config values.
     */
    public static function setBulk(array $values) : void
    {
        static::$values = array_merge(static::$values, $values);
    }
    /**
     * Get config value.
     *
     * @param string $key Config key to retrieve its value.
     *
     * @return mixed Config values.
     */
    public static function get(string $key)
    {
        return static::getValues()[$key];
    }
    /**
     * Get all config values.
     *
     * @return array Config values.
     */
    public static function getValues() : ?array
    {
        return static::$values ?? null;
    }
    /**
     * Detects if the target config key exists.
     *
     * @param string $key Config key you want to check.
     *
     * @return boolean TRUE if the config key exists.
     */
    public static function has(string $key) : bool
    {
        return (static::getValues() !== null) ? array_key_exists($key, static::getValues()) : false;
    }
    /**
     * Get config assert value.
     *
     * @param string $key Config key to retrieve assert value.
     *
     * @return mixed Assert value, callable (string) or [values,] (array).
     */
    public static function getAssert(string $key)
    {
        return static::getAsserts()[$key];
    }
    /**
     * Get config asserts.
     *
     * @return array Asserts.
     */
    public static function getAsserts()
    {
        return static::$asserts;
    }
    /**
     * Detects if the target assert key exists.
     *
     * @param string $key Target assert key.
     *
     * @return bool TRUE is the assert key exists.
     */
    public static function hasAssert(string $key)
    {
        return array_key_exists($key, static::getAsserts());
    }
    /**
     * Returns the loaded configuration file path.
     *
     * @return string Loaded file path.
     */
    public static function loaded()
    {
        return static::$loaded;
    }
    /**
     * Validate config values.
     *
     * @param array $values Key paired string (see Config::$asserts)
     */
    public static function validate(array $values = null)
    {
        if ($values == null) {
            $values = static::getValues();
        }
        $exceptions = [];
        if (is_array($values)) {
            foreach ($values as $k => $v) {
                try {
                    if ($v != null) {
                        static::validator((string) $k, $v);
                    }
                } catch (Exception $e) {
                    $exceptions[] = $e->getMessage();
                }
            }
        }
        if ($exceptions != false) {
            throw new Exception('Invalid configuration: ' . implode('; ', $exceptions));
        }
    }
    /**
     * Validator function.
     *
     * @param string $key Key to validate.
     * @param mixed $value Value to validate.
     */
    protected static function validator(string $key, $value)
    {
        if (array_key_exists($key, static::getAsserts()) == false) {
            // FIXME: Usar new Message()
            // throw new Exception("Unexistant config key <b>$key</b>");
            return;
        }
        $assert = static::getAsserts()[$key];
        $type = gettype($assert);
        switch ($type) {
            case 'string': // a callable name
                if ($assert($value) == false) {
                    throw new ConfigException($key);
                }
            break;
            case 'array':
                if (in_array($value, $assert, true) == false) {
                    throw new ConfigException($key);
                }
            break;
            default:
                throw new Exception(
                    (new Message('Invalid assert type %t, use only type %s or %a'))
                        ->code('%t', $type)
                        ->code('%s', 'string')
                        ->code('%a', 'array')
                );
            break;
        }
    }
}
class ConfigException extends Exception
{
    public function __construct($key, $code = 0, Exception $previous = null)
    {
        $value = Config::get($key);
        if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }
        $message = "Unexpected config value $value for <b>$key</b> config key";
        $assert = Config::getAssert($key);
        if (isset($assert) && is_array($assert)) {
            $message .= ' (expecting <code>' . implode('</code> or <code>', $assert) . '</code>)';
        }
        parent::__construct($message, $code, $previous);
    }
}
