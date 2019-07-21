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

namespace Chevereto\Chevere\Router;

use LogicException;
use Chevereto\Chevere\Message;
use Chevereto\Chevere\Route\Route;

/**
 * TODO: Rename. This class simply returns a route object (runtime or unserialize).
 */
class Resolver
{
    /** @var mixed */
    public $routeSome;

    /** @var bool */
    public $isUnserialized = false;

    public function __construct($routeSome)
    {
        $this->routeSome = $routeSome;
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
                //   ->code('%h', $this->pointer[0].'@'.$this->pointer[1])
          );
        }
    }
}
