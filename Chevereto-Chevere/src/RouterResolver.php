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

namespace Chevereto\Chevere;

use LogicException;

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
class RouterResolver
{
    /** @var mixed */
    public $routeSome;

    /** @var array */
    public $pointer;

    /** @var bool */
    public $isUnserialized = false;

    public function __construct($routeSome, array $pointer)
    {
        $this->routeSome = $routeSome;
        $this->pointer = $pointer;
    }

    public function get(): ?Route
    {
        if ($this->routeSome instanceof Route) {
            return $this->routeSome;
        }
        if (is_string($this->routeSome)) {
            $this->isUnserialized = true;

            return unserialize($this->routeSome, ['allowed_classes' => Route::class]);
        } else {
            throw new LogicException(
              (string) (new Message('Unexpected type %t in routes table %h.'))
                  ->code('%t', gettype($this->routeSome))
                  ->code('%h', $this->pointer[0].'@'.$this->pointer[1])
          );
        }
    }
}
