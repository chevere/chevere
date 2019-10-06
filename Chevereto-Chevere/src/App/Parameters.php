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

namespace Chevere\App;

use LogicException;
use Chevere\ArrayFile\ArrayFile;
use Chevere\Message\Message;
use Chevere\Contracts\App\ParametersContract;

final class Parameters implements ParametersContract
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
    private $keys = [
        // self::CONFIG_FILES => 'array',
        self::API => 'string',
        self::ROUTES => 'array',
    ];

    /** @var ArrayFile The parameters array used to construct the object */
    private $arrayFile;

    /** @var string */
    private $api;

    /** @var array */
    private $routes;

    public function __construct(ArrayFile $arrayFile)
    {
        $this->arrayFile = $arrayFile;
        foreach ($this->arrayFile as $key => $val) {
            $this->assertKeyAvailable($key);
            $this->assertKeyType($key, $val);
        }
        $array = $this->arrayFile->toArray();
        $this->api = $array[static::API];
        $this->routes = $array[static::ROUTES];
    }

    public function api(): string
    {
        return $this->api ?? '';
    }

    public function routes(): array
    {
        return $this->routes ?? [];
    }

    /**
     * Throws a LogicException if the key doesn't exists in $parameters.
     *
     * @param string $key The AppParameter key
     */
    private function assertKeyAvailable(string $key): void
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new LogicException(
                (new Message('Unrecognized %c key "%s".'))
                    ->code('%c', __CLASS__)
                    ->strtr('%s', $key)
                    ->toString()
            );
        }
    }

    /**
     * Throws a LogicException if the key type doesn't meet the type in $keys.
     *
     * @param string $key The AppParameter key
     */
    private function assertKeyType(string $key, $val): void
    {
        $gettype = gettype($val);
        if ($gettype !== $this->keys[$key]) {
            throw new LogicException(
                (new Message('Expecting %s type, %t type provided for key %k in %c.'))
                    ->code('%s', $this->keys[$key])
                    ->code('%t', $gettype)
                    ->code('%k', $key)
                    ->code('%c', $this->arrayFile->pathHandle()->path())
                    ->toString()
            );
        }
    }
}
