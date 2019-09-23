<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Controller\Traits;

/**
 * Provides string document handling for Controllers
 */
trait StringTrait
{
    public function getContent(): string
    {
        return $this->getDocument();
    }

    abstract public function getDocument(): string;
}
