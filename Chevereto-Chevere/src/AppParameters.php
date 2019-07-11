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

namespace Chevereto\Chevere;

use LogicException;

class AppParameters extends Data
{
    const CONFIG_FILES = 'configFiles';

    /**
     * @var string Used to describe the path where App scans for API HTTP Controllers. Target path must be autoloaded.
     *
     * {@example 'api' => 'src/Api'}
     */
    const API = 'api';

    /**
     * @var string Used to describe the array which lists the route files (relative to app).
     *
     * {@example 'routes' => ['routes:dashboard', 'routes:web',]}
     */
    const ROUTES = 'routes';

    /**
     * The keys accepted by this class, with the gettype at right side.
     */
    protected $keys = [
        self::CONFIG_FILES => 'array',
        self::API => 'string',
        self::ROUTES => 'array',
    ];

    /** @var array The parameters array used to construct the object */
    protected $parameters;

    /** @var string|null The file source (for instances created using ::createFromFile) */
    protected $sourceFilepath;

    /**
     * @param array  $parameters The parameters array
     * @param string $context    The context of the source $parameters
     */
    public function __construct(array $parameters, string $context = 'array')
    {
        $this->parameters = $parameters;
        $this->context = $context;
        $this->validate($parameters);
        $this->setData($parameters);
    }

    /**
     * Throws a LogicException if the thing doesn't validate.
     */
    protected function validate(array $parameters): void
    {
        foreach ($parameters as $key => $val) {
            $this->validateKeyExists($key);
            $this->validateKeyType($key, $val);
        }
    }

    /**
     * Throws a LogicException if the key doesn't exists in $parameters.
     *
     * @param string $key The AppParameter key
     */
    protected function validateKeyExists(string $key): void
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new LogicException(
                (string)
                    (new Message('Unrecognized %c key "%s".'))
                        ->code('%c', __CLASS__)
                        ->strtr('%s', $key)
            );
        }
    }

    /**
     * Throws a LogicException if the key type doesn't meet the type in $keys.
     *
     * @param string $key The AppParameter key
     */
    protected function validateKeyType(string $key, $val): void
    {
        $gettype = gettype($val);
        if ($gettype !== $this->keys[$key]) {
            throw new LogicException(
                (string)
                    (new Message('Expecting type %s, %t provided for key "%k" in %c.'))
                        ->code('%s', $this->keys[$key])
                        ->code('%t', $gettype)
                        ->strtr('%k', $key)
                        ->code('%c', $this->context)
            );
        }
    }

    protected function setSourceFilepath(string $filepath): self
    {
        $this->sourceFilepath = $filepath;

        return $this;
    }

    /**
     * Creates AppParameters instance from file.
     *
     * @param string $fileHandle filehandle
     */
    public static function createFromFile(PathHandle $pathHandle)
    {
        $arrayFile = new ArrayFile($pathHandle);

        return new AppParameters($arrayFile->toArray(), $arrayFile->getFilepath());
    }
}
