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

namespace Chevere\Components\Controller\Traits;

/**
 * Provides string document handling for Controllers
 */
trait ResponseStringTrait
{
    public function getContent(): string
    {
        return $this->getDocument();
    }

    abstract public function getDocument(): string;
}
