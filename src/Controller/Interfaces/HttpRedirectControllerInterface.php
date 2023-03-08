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

namespace Chevere\Controller\Interfaces;

use Chevere\HttpController\Interfaces\HttpRedirectControllerInterface as NewHttpRedirectControllerInterface;

/**
 * @deprecated Use Chevere\HttpController\Interfaces\HttpRedirectControllerInterface instead.
 * This will be removed in 3.0.0
 */
interface HttpRedirectControllerInterface extends NewHttpRedirectControllerInterface
{
}
