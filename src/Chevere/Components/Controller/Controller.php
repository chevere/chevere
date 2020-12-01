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

namespace Chevere\Components\Controller;

use Chevere\Components\Action\Action;
use Chevere\Interfaces\Controller\ControllerInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Controller extends Action implements ControllerInterface
{
    public function withSetUp(): self
    {
        return $this;
    }
}
