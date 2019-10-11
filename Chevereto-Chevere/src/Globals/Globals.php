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

namespace Chevere\Globals;

use InvalidArgumentException;
use LogicException;
use Chevere\Message\Message;

/**
 * Provides read-only access for superglobals.
 * 
 * @method array server()
 * @method array post()
 * @method array files()
 * @method array cookie()
 * @method array session()
 * @method array request()
 * @method array env()
 */
final class Globals
{
    const METHODS = [
        'argc',
        'argv',
        'server',
        'get',
        'post',
        'files',
        'cookie',
        'session',
        'request',
        'env',
    ];

    const KEYS = [
        'argc',
        'argv',
        '_SERVER',
        '_GET',
        '_POST',
        '_FILES',
        '_COOKIE',
        '_SESSION',
        '_REQUEST',
        '_ENV',
    ];

    /** @var array */
    private $globals;

    public function __construct(array $globals)
    {
        $this->globals = $globals;
        $this->assertValid();
    }

    public function toArray(): array
    {
        return $this->globals;
    }

    public function __call($name, $arguments)
    {
        $key = array_search($name, static::METHODS);
        if (null === $key) {
            throw new LogicException(
                (new Message('Call to undefined method %method%'))
                    ->code('%method%', $name)
                    ->toString()
            );
        }
        $globalKey = static::KEYS[$key];

        return $this->globals[$globalKey] ?? [];
    }

    private function assertValid(): void
    {
        $keys = array_keys($this->globals);
        $diff = array_diff(static::KEYS, $keys);
        if (count($diff) == count(static::KEYS)) {
            throw new InvalidArgumentException(
                (new Message('Invalid %globals% array passed'))
                    ->code('%globals%', '$GLOBALS')
                    ->toString()
            );
        }
    }
}
