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

namespace Chevere\App;

use RuntimeException;
use Chevere\Message;
use Chevere\Contracts\App\CheckoutContract;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class Checkout
{
    public function __construct()
    {
        if (!file_put_contents(App::BUILD_FILEPATH, (string) time())) {
            throw new RuntimeException(
                (new Message('Unable to checkout to %file%'))
                    ->code('%file', App::BUILD_FILEPATH)
                    ->toString()
            );
        }
    }
}
