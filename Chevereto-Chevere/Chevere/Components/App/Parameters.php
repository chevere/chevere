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
use Chevere\Contracts\App\ParametersContract;

final class Parameters implements ParametersContract
{
    /**
     * The keys accepted by this class, with the gettype at right side.
     */
    private $keys = [
        static::KEY_API => 'string',
        static::KEY_ROUTES => 'array',
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
        $this->api = $array[static::KEY_API];
        $this->routes = $array[static::KEY_ROUTES];
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
                    ->code('%c', $this->arrayFile->path()->absolute())
                    ->toString()
            );
        }
    }
}
