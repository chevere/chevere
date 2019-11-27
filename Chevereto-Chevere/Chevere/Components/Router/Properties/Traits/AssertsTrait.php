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

use Chevere\Components\Message\Message;
use TypeError;

trait AssertsTrait
{
    private function assertString($var): void
    {
        if (!is_string($var)) {
            throw new TypeError(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'string')
                    ->code('%provided%', gettype($var))
                    ->toString()
            );
        }
    }

    private function assertInt($var): void
    {
        if (!is_int($var)) {
            throw new TypeError(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'int')
                    ->code('%provided%', gettype($var))
                    ->toString()
            );
        }
    }

    private function assertStringNotEmpty(string $var): void
    {
        if ('' == $var) {
            throw new TypeError(
                (new Message('Empty string provided'))
                    ->toString()
            );
        }
    }

    private function assertArrayNotEmpty(array $var): void
    {
        if (empty($var)) {
            throw new TypeError(
                (new Message('Empty array provided'))
                    ->toString()
            );
        }
    }
}
