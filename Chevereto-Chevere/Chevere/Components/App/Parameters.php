<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App;

use LogicException;

use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Contracts\App\ParametersContract;
use InvalidArgumentException;

final class Parameters implements ParametersContract
{
    /**
     * The keys accepted by this class, with the gettype at right side.
     */
    private $types = [
        self::KEY_API => 'string',
        self::KEY_ROUTES => 'array',
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
        $this->assertKeys();
        $array = $this->arrayFile->toArray();
        if (isset($array[static::KEY_API])) {
            $this->api = $array[static::KEY_API];
        }
        if (isset($array[static::KEY_ROUTES])) {
            $this->routes = $array[static::KEY_ROUTES];
        }
    }

    public function withAddedRoutePaths(Path ...$paths): ParametersContract
    {
        $new = clone $this;
        if (!isset($new->routes)) {
            $new->routes = [];
        }
        foreach ($paths as $path) {
            if (in_array($path->absolute(), $new->routes)) {
                throw new InvalidArgumentException(
                    (new Message('Route path %path% was already added'))
                        ->code('%path%', $path->identifier())
                        ->toString()
                );
            }
            $new->routes[] = $path->absolute();
        }

        return $new;
    }

    public function hasParameters(): bool
    {
        return $this->hasApi() || $this->hasRoutes();
    }

    public function hasApi(): bool
    {
        return isset($this->api);
    }

    public function hasRoutes(): bool
    {
        return isset($this->routes);
    }

    public function api(): string
    {
        return $this->api;
    }

    public function routes(): array
    {
        return $this->routes;
    }

    private function assertKeys(): void
    {
        foreach ($this->arrayFile as $key => $val) {
            $this->assertKeyAvailable($key);
            $this->assertKeyType($key, gettype($val));
        }
    }

    /**
     * Throws a LogicException if the key doesn't exists in $parameters.
     *
     * @param string $key The AppParameter key
     */
    private function assertKeyAvailable(string $key): void
    {
        if (!array_key_exists($key, $this->types)) {
            throw new LogicException(
                (new Message('Unrecognized %className% key "%key%"'))
                    ->code('%className%', __CLASS__)
                    ->strtr('%key%', $key)
                    ->toString()
            );
        }
    }

    /**
     * Throws a LogicException if the key type doesn't meet the type in $keys.
     *
     * @param string $key The AppParameter key
     * @param string $key The value type
     */
    private function assertKeyType(string $key, string $gettype): void
    {
        if ($gettype !== $this->types[$key]) {
            throw new LogicException(
                (new Message('Expecting %type% type, %gettype% type provided for key %key% in %path%'))
                    ->code('%type%', $this->types[$key])
                    ->code('%gettype%', $gettype)
                    ->code('%key%', $key)
                    ->code('%path%', $this->arrayFile->path()->absolute())
                    ->toString()
            );
        }
    }
}
