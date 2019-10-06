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
use Chevere\Cache\Exceptions\CacheNotFoundException;
use LogicException;
use Chevere\Message\Message;
use Chevere\Contracts\Api\ApiContract;
use Chevere\FileReturn\Exceptions\FileNotFoundException;
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

    public function __construct()
    { }

    public static function fromMaker(Maker $maker): ApiContract
    {
        $api = new static();
        $api::$api = $maker->api();
        $maker = $maker->withCache();
        return $api;
    }

    public static function fromCache(): ApiContract
    {
        $cache = new Cache('api');
        $api = new static();
        try {
            $api::$api = $cache->get('api')->raw();
        } catch (FileNotFoundException $e) {
            throw new CacheNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
        return $api;
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

    /**
     * @return string The the endpoint basename for the given URI.
     */
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
