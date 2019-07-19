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

namespace Chevereto\Chevere\App;

use RuntimeException;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
class AppCheckout
{
    public function __construct(string $filename)
    {
        $fh = fopen($filename, 'w');
        if (false === $fh) {
            throw new RuntimeException(
                (string) (new Message('Unable to open %f for writing'))->code('%f', $filename)
            );
        }
        if (!@fwrite($fh, (string) time())) {
            throw new RuntimeException(
                (string) (new Message('Unable to write to %f'))->code('%f', $filename)
            );
        }
        if (!@fclose($fh)) {
            throw new RuntimeException(
                (string) (new Message('Unable to close %f'))->code('%f', $filename)
            );
        }
    }
}
