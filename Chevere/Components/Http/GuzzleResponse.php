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

namespace Chevere\Components\Http;

use Chevere\Components\Http\Interfaces\GuzzleResponseInterface;
use GuzzleHttp\Psr7\Response as BaseResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class GuzzleResponse extends BaseResponse implements GuzzleResponseInterface
{
    public function withJsonApi(StreamInterface $jsonApi): ResponseInterface
    {
        $new = clone $this;

        return $new->withJsonApiHeaders()->withBody($jsonApi);
    }

    public function withJsonApiHeaders(): ResponseInterface
    {
        $new = clone $this;

        return $new->withAddedHeader('Content-Type', 'application/vnd.api+json');
    }
}
