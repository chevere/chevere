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

namespace Chevere\Contracts\Api;

use Chevere\Contracts\Cache\CacheContract;

interface ApiContract
{
    const CACHE_ID = 'api';

    public function withApiMaker(ApiMakerContract $maker): ApiContract;

    public function withCache(CacheContract $cache): ApiContract;

    public function toArray(): array;

    public function hasMaker(): bool;

    public function hasCache(): bool;

    public function apiMaker(): ApiMakerContract;

    public function cache(): CacheContract;

    public function endpoint(string $uriKey): array;

    /**
     * @return string the the endpoint basename for the given URI
     */
    public function endpointKey(string $uri): string;
}
