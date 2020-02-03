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

namespace Chevere\Components\Api;

use LogicException;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Cache\Traits\CacheAccessTrait;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\Api\Interfaces\ApiMakerInterface;
use Chevere\Components\Cache\Interfaces\CacheInterface;

final class Api implements ApiInterface
{
    use CacheAccessTrait;

    private array $array;

    private ApiMakerInterface $maker;

    public function withApiMaker(ApiMakerInterface $maker): ApiInterface
    {
        $new = clone $this;
        $new->maker = $maker;
        $new->array = $new->maker->api();

        return $new;
    }

    public function withCache(CacheInterface $cache): ApiInterface
    {
        $new = clone $this;
        $new->cache = $cache;
        try {
            $new->array = $new->cache
                ->get(
                    new CacheKey(CacheKeys::API)
                )
                ->raw();
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

    public function apiMaker(): ApiMakerInterface
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
