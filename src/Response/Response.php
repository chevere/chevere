<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Response;

use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Traits\ResponseTrait;

final class Response implements ResponseInterface
{
    use ResponseTrait;
}
