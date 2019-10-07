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

use Chevere\Http\Response;
use Chevere\Contracts\App\AppContract;

interface ControllerContract
{
    public function __construct(AppContract $app);

    public function getContent(): string;

    public function content(): string;

    public function withResponse(Response $response): ControllerContract;
}
