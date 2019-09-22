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

use JsonSerializable;
use Chevere\JsonApi\EncodedDocument;

trait JsonApiTrait
{
    /** @var EncodedDocument */
    private $document;

    public function setDocument(EncodedDocument $json): void
    {
        $this->document = $json;
    }

    public function getContent(): string
    {
        return $this->document->toString();
    }
}
