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

namespace Chevere\Http;

use GuzzleHttp\Psr7\Response as BaseResponse;
use Psr\Http\Message\StreamInterface;

final class GuzzleResponse extends BaseResponse
{
    /**
     * {@inheritdoc}
     */
    public function withJsonApi(StreamInterface $jsonApi): BaseResponse
    {
        $new = clone $this;
        return $new->withJsonApiHeaders()->withBody($jsonApi);
    }

    /**
     * {@inheritdoc}
     */
    public function withJsonApiHeaders(): BaseResponse
    {
        $new = clone $this;
        return $new->withAddedHeader('Content-Type', 'application/vnd.api+json');
    }
}
