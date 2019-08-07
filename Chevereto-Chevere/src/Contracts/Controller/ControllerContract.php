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

namespace Chevere\Contracts\Controller;

use Chevere\HttpFoundation\Response;
use Chevere\Contracts\App\AppContract;

interface ControllerContract
{
    public function __construct(AppContract $app);

    public function setResponse(Response $response): ControllerContract;

    public static function description(): string;

    public static function resources(): array;

    public static function parameters(): array;
}
