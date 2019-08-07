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

namespace Chevere\Router;

use LogicException;
use Chevere\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\ResolverContract;

/**
 * TODO: Rename. This class simply returns a route object (runtime or unserialize).
 */
final class Resolver implements ResolverContract
{
    /** @var mixed */
    public $routeSome;

    /** @var bool */
    public $isUnserialized = false;

    public function __construct($routeSome)
    {
        $this->routeSome = $routeSome;
    }

    public function get(): RouteContract
    {
        if ($this->routeSome instanceof RouteContract) {
            return $this->routeSome;
        }
        if (is_string($this->routeSome)) {
            $this->isUnserialized = true;

            return unserialize($this->routeSome, ['allowed_classes' => Route::class]);
        } else {
            throw new LogicException(
                (new Message('Unexpected type %t in routes table %h.'))
                    ->code('%t', gettype($this->routeSome))
                    ->toString()
            );
        }
    }
}
