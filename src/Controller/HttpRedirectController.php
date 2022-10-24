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

use Chevere\Controller\Interfaces\HttpRedirectControllerInterface;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Psr\Http\Message\UriInterface;

abstract class HttpRedirectController extends HttpController implements HttpRedirectControllerInterface
{
    private ?UriInterface $uri;

    private int $status = 302;

    public function withUri(UriInterface $uri): static
    {
        $new = clone $this;
        $new->uri = $uri;

        return $new;
    }

    public function withStatus(int $status): static
    {
        $this->assertStatus($status);
        $new = clone $this;
        $new->status = $status;

        return $new;
    }

    final public function uri(): UriInterface
    {
        return $this->uri
            ?? throw new LogicException(message('No uri set'));
    }

    final public function status(): int
    {
        return $this->status;
    }

    final protected function assertStatus(int $status): void
    {
        $statusCodes = array_keys(static::STATUSES);
        if (! in_array($status, $statusCodes, true)) {
            throw new InvalidArgumentException(
                message('Invalid status code %status% provided, must be one of %statuses%')
                    ->withCode('%status%', strval($status))
                    ->withCode('%statuses%', implode(', ', $statusCodes))
            );
        }
    }
}
