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

namespace Chevere\Components\Api;

use LogicException;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Cache\Traits\CacheAccessTrait;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\Api\MakerContract;
use Chevere\Contracts\Cache\CacheContract;

final class Api implements ApiContract
{
    use CacheAccessTrait;

    /** @var array The API array */
    private $array;

    /** @var MakerContract */
    private $maker;

    public function withMaker(MakerContract $maker): ApiContract
    {
        $new = clone $this;
        $new->maker = $maker;
        $new->array = $new->maker->api();

        return $new;
    }

    public function withCache(CacheContract $cache): ApiContract
    {
        $new = clone $this;
        $new->cache = $cache;
        try {
            $new->array = $new->cache->get(CacheKeys::API)->return();
        } catch (FileNotFoundException $e) {
            throw new CacheNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $new;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function hasMaker(): bool
    {
        return isset($this->maker);
    }

    public function maker(): MakerContract
    {
        return $this->maker;
    }

    public function endpoint(string $uriKey): array
    {
        $key = $this->endpointKey($uriKey);
        if ($key) {
            $subKey = ltrim($uriKey, '/') == $key ? '' : $uriKey;

            return $this->array[$key][$subKey];
        }
        throw new LogicException(
            (new Message('No endpoint defined for the %s URI'))
                ->code('%s', $uriKey)
                ->toString()
        );
    }

    public function endpointKey(string $uri): string
    {
        $endpoint = ltrim($uri, '/');
        $base = strtok($endpoint, '/');
        if (!isset($this->array[$base])) {
            throw new LogicException(
                (new Message('No API endpoint key for the %s URI'))
                    ->code('%s', $uri)
                    ->toString()
            );
        }

        return $base;
    }
}
