<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

class AppParameters extends Data
{
    const CONFIG_FILES = 'configFiles';
    const APIS = 'apis';
    const ROUTES = 'routes';

    const KEYS = [
        self::CONFIG_FILES,
        self::APIS,
        self::ROUTES,
    ];

    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $v) {
            if (false == in_array($key, static::KEYS)) {
                throw new CoreException(
                    (new Message('Unrecognized %c key "%s".'))
                        ->code('%c', __CLASS__)
                        ->strtr('%s', $key)
                );
            }
        }
        $this->setData($parameters);
    }

    public static function createFromFile(string $fileHandle)
    {
        try {
            $arrayFile = new ArrayFile($fileHandle);
        } catch (Exception $e) {
            throw new CoreException($e);
        }

        return new static($arrayFile->toArray());
    }
}
