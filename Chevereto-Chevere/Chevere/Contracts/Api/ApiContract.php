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

namespace Chevere\Contracts\Api;

use Chevere\Components\Cache\Cache;

interface ApiContract
{
    public function withMaker(MakerContract $maker): ApiContract;

    public function withCache(Cache $cache): ApiContract;

    public function toArray(): array;

    public function hasMaker(): bool;

    public function hasCache(): bool;

    public function maker(): MakerContract;

    public function cache(): Cache;

    public function endpoint(string $uriKey): array;

    /**
     * @return string The the endpoint basename for the given URI.
     */
    public function endpointKey(string $uri): string;
}
