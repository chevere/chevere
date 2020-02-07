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

namespace Chevere\Components\Api\Interfaces;

use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Common\Interfaces\ToArrayInterface;

interface ApiInterface extends ToArrayInterface
{
    const CACHE_ID = 'api';

    public function withApiMaker(ApiMakerInterface $maker): ApiInterface;

    public function withCache(CacheInterface $cache): ApiInterface;

    public function hasMaker(): bool;

    public function hasCache(): bool;

    public function apiMaker(): ApiMakerInterface;

    public function cache(): CacheInterface;

    public function endpoint(string $uriKey): array;

    /**
     * @return string the the endpoint basename for the given URI
     */
    public function endpointKey(string $uri): string;
}
