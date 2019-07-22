<?php

//ok
declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Interfaces;

interface ErrorHandlerInterface
{
    public static function error($severity, $message, $file, $line): void;

    public static function exception($e): void;
}
