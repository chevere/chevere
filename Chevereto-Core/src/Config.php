<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// TODO: Use object instance.
namespace Chevereto\Core;

use Exception;

use Chevereto\Core\Message;
use Chevereto\Core\Path;

// use Symfony\Component\HttpFoundation\Request;

/**
 * App configuration (runtime)
 */
class Config extends Data
{
    // Config keys

    const DEBUG = 'debug';
    const LOCALE = 'locale';
    const DEFAULT_CHARSET = 'defaultCharset';
    const ERROR_REPORTING_LEVEL = 'errorReportingLevel';
    const ERROR_HANDLER = 'errorHandler';
    const EXCEPTION_HANDLER = 'exceptionHandler';
    const URI_SCHEME = 'uriScheme';
    const TIMEZONE = 'timeZone';
    // Cache modes
    // const CACHE_MODE_ON = 'on';
    // const CACHE_MODE_OFF = 'off';
    // const CACHE_MODE_AUTO = 'auto';
    // Cache modes apply
    // const ROUTER_CACHE_MODE = 'routerCacheMode';

    private $_data = [];
    protected $loadedFiles = [];

    protected $asserts = [
        self::DEBUG             => [0, 1],
        // locale
        // charset
        // error reporting
        self::ERROR_HANDLER     => 'is_callable',
        self::EXCEPTION_HANDLER => 'is_callable',
        self::URI_SCHEME       => ['http', 'https'],
        self::TIMEZONE          => __NAMESPACE__ . '\Validate::timezone',
    ];

    public function __construct(string $fileHandle = null)
    {
        if (null != $fileHandle) {
            return $this->processFromFile($fileHandle);
        }
    }
    public function addFile(string $fileHandle) : self
    {
        $filepath = Path::fromHandle($fileHandle);
        $this->loadedFiles[] = $filepath;
        $array = Load::php($filepath);
        return $this->dataAdder($array);
    }
    public function addArray(array $array) : self
    {
        return $this->dataAdder($array);
    }
    protected function dataAdder(array $data) : self
    {
        $this->_data = array_replace_recursive($this->_data, $data);
        return $this;
    }
    public function process() : self
    {
        try {
            $this->validate();
        } catch (Exception $e) {
            throw new CoreException(
                (new Message($e->getMessage() . ' ' . 'at %s'))->b('%s', '0000000000000000')
            );
        }
        $this->addData($this->_data);
        return $this;
    }

    public function processFromFile(string $fileHandle) : self
    {
        $this->addFile($fileHandle);
        return $this->process();
    }
    public function processFromArray(array $config) : self
    {
        $this->addArray($config);
        return $this->process();
    }
    /**
     * Get config assert value.
     *
     * @param string $key Config key to retrieve assert value.
     *
     * @return mixed Assert value, callable (string) or [values,] (array).
     */
    public function getAssert(string $key)
    {
        return $this->getAsserts()[$key];
    }
    /**
     * Get config asserts.
     *
     * @return array Asserts.
     */
    public function getAsserts() : array
    {
        return $this->asserts;
    }
    /**
     * Detects if the target assert key exists.
     *
     * @param string $key Target assert key.
     *
     * @return bool TRUE is the assert key exists.
     */
    public function hasAssert(string $key) : bool
    {
        return array_key_exists($key, $this->getAsserts());
    }
    /**
     * Returns the loaded configuration file path.
     *
     * @return string Loaded file path.
     */
    public function getLoadedFiles() : array
    {
        return $this->loadedFiles;
    }
    /**
     * Validate config values.
     */
    protected function validate()
    {
        $exceptions = [];
        foreach ($this->_data as $k => $v) {
            try {
                if ($v != null) {
                    $this->validator((string) $k, $v);
                }
            } catch (Exception $e) {
                $exceptions[] = $e->getMessage();
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
    protected function validator(string $key, $value)
    {
        if (array_key_exists($key, $this->getAsserts()) == false) {
            // FIXME: Usar new Message()
            // throw new Exception("Unexistant config key <b>$key</b>");
            return;
        }
        $assert = $this->getAsserts()[$key];
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
                throw new CoreException(
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
        $value = 'test' ?? Config::get($key);
        if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }
        $message = "Unexpected config value $value for <b>$key</b> config key";
        $assert = 'try' ?? Config::getAssert($key);
        if (isset($assert) && is_array($assert)) {
            $message .= ' (expecting <code>' . implode('</code> or <code>', $assert) . '</code>)';
        }
        parent::__construct($message, $code, $previous);
    }
}
