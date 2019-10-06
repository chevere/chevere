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

use Chevere\Api\Maker;

interface ApiContract
{
    public static function fromMaker(Maker $maker): ApiContract;

    public static function fromCache(): ApiContract;

    public function get(): array;

    public static function endpoint(string $uriKey): array;

    /**
     * @return string The the endpoint basename for the given URI.
     */
    public static function endpointKey(string $uri): string;
}
