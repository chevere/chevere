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

use Chevere\Cache\Cache;
use LogicException;
use Chevere\Message;
use Chevere\Contracts\Api\ApiContract;
use Chevere\FileReturn\FileReturn;
use Chevere\Path\PathHandle;
use Chevere\Stopwatch;

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Api implements ApiContract
{
    /** @var string Prefix used for endpoints without a defined resource (/endpoint) */
    const METHOD_ROOT_PREFIX = '_';

    /** @var array */
    private static $api;

    public function __construct(Maker $maker = null)
    {
        if (isset($maker)) {
            self::$api = $maker->api();
            $maker->cache();
        } else {
            $sw = new Stopwatch();
            $cache = new Cache('api');
            $isCached = $cache->exists('api');
            $sw->stop();
            dd($isCached, $sw->records());
            self::$api = $cache->get('api')->raw();
        }
    }

    public function get(): array
    {
        return self::$api;
    }

    public static function endpoint(string $uriKey): array
    {
        $key = self::endpointKey($uriKey);
        if ($key) {
            $subKey = ltrim($uriKey, '/') == $key ? '' : $uriKey;

            return self::$api[$key][$subKey];
        }

        throw new LogicException(
            (new Message('No endpoint defined for the %s URI.'))
                ->code('%s', $uriKey)
                ->toString()
        );
    }

    public static function endpointKey(string $uri): string
    {
        $endpoint = ltrim($uri, '/');
        $base = strtok($endpoint, '/');

        if (!isset(self::$api[$base])) {
            throw new LogicException(
                (new Message('No API endpoint key for the %s URI.'))
                    ->code('%s', $uri)
                    ->toString()
            );
        }

        return $base;
    }
}
