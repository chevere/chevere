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

namespace Chevere\Api;

use LogicException;
use Chevere\Message;

/**
 * Api provides a static method to read the exposed API.
 */
final class Api
{
    /** @var string Prefix used for endpoints without a defined resource (/endpoint) */
    const METHOD_ROOT_PREFIX = '_';

    /** @var array */
    private static $api;

    public function __construct(Maker $api)
    {
        static::$api = $api->api();
    }

    public static function endpoint(string $uriKey): ?array
    {
        $key = static::endpointKey($uriKey);
        if ($key) {
            $subKey = $uriKey == $key ? '' : $uriKey;

            return static::$api[$key][$subKey];
        }

        return null;
    }

    public static function endpointKey(string $uri): string
    {
        $endpoint = ltrim($uri, '/');
        $base = strtok($endpoint, '/');

        if (!isset(static::$api[$base])) {
            throw new LogicException(
                (new Message('No API for the %s URI.'))
                    ->code('%s', $uri)
                    ->toString()
            );
        }

        return $base;
    }
}
