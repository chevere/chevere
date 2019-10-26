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

namespace Chevere\Components\Http;

use Chevere\Contracts\Http\RequestContract;

final class RequestContainer
{
    private static $instance;

    public function __construct(RequestContract $request)
    {
        self::$instance = $request;
    }

    public static function getInstance(): RequestContract
    {
        return self::$instance;
    }
}
