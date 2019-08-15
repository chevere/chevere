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

namespace Chevere\Runtime;

use Exception;
use LogicException;
use InvalidArgumentException;
use Chevere\Data\Data;
use Chevere\Path\Path;
use Chevere\Message;
use Chevere\ArrayFile;
use Chevere\Validate;

/**
 * Runtime configurator.
 */
final class Config
{
    // Config keys
    const DEBUG = 'debug';
    const LOCALE = 'locale';
    const DEFAULT_CHARSET = 'defaultCharset';
    const ERROR_REPORTING_LEVEL = 'errorReportingLevel'; // Not set, but its current value gets collected in the data array
    const ERROR_HANDLER = 'errorHandler';
    const EXCEPTION_HANDLER = 'exceptionHandler';
    const URI_SCHEME = 'uriScheme';
    const TIMEZONE = 'timeZone';

    /** @var Data */
    public $data;

    /** @var array Loaded configuration filepaths. */
    private $loadedFilepaths = [];

    private $asserts = [
        self::DEBUG => [0, 1],
        // locale
        // charset
        // error reporting
        self::ERROR_HANDLER => 'is_callable',
        self::EXCEPTION_HANDLER => 'is_callable',
        self::URI_SCHEME => ['http', 'https'],
        self::TIMEZONE => Validate::class.'::timezone',
    ];

    public function __construct()
    {
        $this->data = new Data();
    }

    public function addFile(string $filepath): self
    {
        $fileHandle = Path::handle($filepath);
        $arrayFile = new ArrayFile($fileHandle);
        $this->loadedFilepaths[] = $filepath;

        return $this->dataAdder($arrayFile->toArray());
    }

    public function addArray(array $array): self
    {
        return $this->dataAdder($array);
    }

    /**
     * Get config assert value.
     *
     * @param string $key config key to retrieve assert value
     *
     * @return mixed assert value, callable (string) or [values,] (array)
     */
    public function getAssert(string $key)
    {
        return $this->asserts[$key];
    }

    /**
     * Detects if the target assert key exists.
     *
     * @param string $key target assert key
     *
     * @return bool TRUE is the assert key exists
     */
    public function hasAssert(string $key): bool
    {
        return array_key_exists($key, $this->asserts);
    }

    /**
     * Returns the loaded configuration filepaths.
     *
     * @return array Loaded filepaths, []
     */
    public function getLoadedFilepaths(): array
    {
        return $this->loadedFilepaths;
    }

    private function dataAdder(array $data): self
    {
        foreach (array_keys($data) as $key) {
            $fnName = 'set'.ucwords($key);
            if (!method_exists(Runtime::class, $fnName)) {
                throw new LogicException(
                    (new Message('Unrecognized %c key "%s".'))
                        ->code('%c', __CLASS__)
                        ->strtr('%s', $key)
                        ->toString()
                );
            }
            $this->data->setKey($key, $data[$key]);
        }

        return $this;
    }

    public function validate(): self
    {
        $exceptions = [];
        foreach ($this->data as $k => $v) {
            try {
                if ($v != null) {
                    $this->validator((string) $k, $v);
                }
            } catch (Exception $e) {
                $exceptions[] = $e->getMessage();
            }
        }
        if ($exceptions != false) {
            throw new LogicException('Invalid configuration: '.implode('; ', $exceptions));
        }

        return $this;
    }

    /**
     * Validator validates only if an assert is declared.
     *
     * @param string $key   key to validate
     * @param mixed  $value value to validate
     */
    private function validator(string $key, $value)
    {
        if (!array_key_exists($key, $this->asserts)) {
            return;
        }
        $assert = $this->asserts[$key];
        $type = gettype($assert);
        switch ($type) {
            case 'string': // a callable name
                if ($assert($value) == false) {
                    throw new ConfigException($key);
                }
                break;
            case 'array':
                if (!in_array($value, $assert, true)) {
                    throw new ConfigException($key);
                }
                break;
            default:
                throw new InvalidArgumentException(
                    (new Message('Invalid assert type %t, use only type %s or %a'))
                        ->code('%t', $type)
                        ->code('%s', 'string')
                        ->code('%a', 'array')
                );
        }
    }
}
class ConfigException extends Exception
{
    public function __construct($key, $code = 0, Exception $previous = null)
    {
        // $value = 'test' ?? Config::get($key);
        // TODO: Re-factor this
        $value = '00000000000000000xTest';
        // if (is_bool($value)) {
        //     $value = $value ? 'TRUE' : 'FALSE';
        // }
        $message = "Unexpected config value $value for <b>$key</b> config key";
        // $assert = 'try' ?? Config::getAssert($key);
        $assert = '00000000000000000xTry';
        $message .= ' (expecting <code>'.$assert.'</code>)';
        parent::__construct($message, $code, $previous);
    }
}
