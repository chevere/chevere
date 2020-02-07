<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App;

use Chevere\Components\App\Exceptions\ParametersDuplicatedException;
use Chevere\Components\App\Exceptions\ParametersWrongKeyException;
use Chevere\Components\App\Exceptions\ParametersWrongTypeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\ArrayFile\Interfaces\ArrayFileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\AppPathInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;

/**
 * Application parameters container.
 */
final class Parameters implements ParametersInterface
{
    /**
     * The keys accepted by this class, with the gettype at right side.
     */
    private array $types = [
        ParametersInterface::KEY_API => 'string',
        ParametersInterface::KEY_ROUTES => 'array',
    ];

    private ArrayFileInterface $arrayFile;

    private string $api;

    private array $routes;

    public function __construct(ArrayFileInterface $arrayFile)
    {
        $this->arrayFile = $arrayFile;
        $this->assertKeys();
        $array = $this->arrayFile->array();
        if (isset($array[ParametersInterface::KEY_API])) {
            $this->api = $array[ParametersInterface::KEY_API];
        }
        if (isset($array[ParametersInterface::KEY_ROUTES])) {
            $routes = $array[ParametersInterface::KEY_ROUTES];
            $this->routes = [];
            foreach ($routes as $route) {
                $this->routes[] = (new AppPath($route))->relative();
            }
        }
    }

    public function withAddedRoutePaths(AppPathInterface ...$paths): ParametersInterface
    {
        $new = clone $this;
        if (!isset($new->routes)) {
            $new->routes = [];
        }
        foreach ($paths as $path) {
            if (in_array($path->relative(), $new->routes)) {
                throw new ParametersDuplicatedException(
                    (new Message('Route path %path% was already added'))
                        ->code('%path%', $path->absolute())
                        ->toString()
                );
            }
            $new->routes[] = $path->relative();
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

    public function api(): string
    {
        return $this->api;
    }

    public function hasRoutes(): bool
    {
        return isset($this->routes);
    }

    public function routes(): array
    {
        return $this->routes;
    }

    private function assertKeys(): void
    {
        foreach ($this->arrayFile->array() as $key => $val) {
            $this->assertValidKeys($key);
            $this->assertKeyType($key, gettype($val));
        }
    }

    /**
     * @param string $key The AppParameter key
     */
    private function assertValidKeys(string $key): void
    {
        if (!array_key_exists($key, $this->types)) {
            throw new ParametersWrongKeyException(
                (new Message('Unrecognized %className% key "%key%"'))
                    ->code('%className%', self::class)
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
            throw new ParametersWrongTypeException(
                (new Message('Expecting %type% type, %gettype% type provided for key %key% in %path%'))
                    ->code('%type%', $this->types[$key])
                    ->code('%gettype%', $gettype)
                    ->code('%key%', $key)
                    ->code('%path%', $this->arrayFile->file()->path()->absolute())
                    ->toString()
            );
        }
    }
}
