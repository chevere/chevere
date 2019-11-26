<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router\Properties\Traits;

use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Message\Message;

trait AssertArrayTrait
{
    /** @var array */
    private $value;

    private function assertArray(): void
    {
        if (empty($this->value)) {
            throw new RouterPropertyException(
                (new Message('Empty argument array'))
                    ->toString()
            );
        }
    }
}
