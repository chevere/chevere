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

namespace Chevere\Controller;

use Chevere\HttpController\HttpController as NewHttpController;

/**
 * @deprecated use Chevere\HttpController\HttpController instead.
 * This will be removed in 3.0.0
 */
abstract class HttpController extends NewHttpController
{
}
