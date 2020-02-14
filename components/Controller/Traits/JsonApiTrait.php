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

use Chevere\Components\JsonApi\EncodedDocument;

/**
 * Provides JSON API document handling for Controllers
 */
trait JsonApiTrait
{
    public function getContent(): string
    {
        return $this->getDocument()->toString();
    }

    abstract public function getDocument(): EncodedDocument;
}
