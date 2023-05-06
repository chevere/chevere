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

namespace Chevere\HttpController;

use Chevere\HttpController\Interfaces\HttpRedirectControllerInterface;
use function Chevere\Message\message;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\integer;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\object;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Psr\Http\Message\UriInterface;

abstract class HttpRedirectController extends HttpController implements HttpRedirectControllerInterface
{
    private ?UriInterface $uri;

    /**
     * @var int<300, 399>
     */
    private int $status = 302;

    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp(
            uri: object(UriInterface::class),
            status: integer()
                ->withAccept(...static::STATUSES),
        );
    }

    final public function withUri(UriInterface $uri): static
    {
        $new = clone $this;
        $new->setUri($uri);

        return $new;
    }

    /**
     * @param int<300, 399> $status
     */
    final public function withStatus(int $status): static
    {
        $new = clone $this;
        $new->setStatus($status);

        return $new;
    }

    final public function uri(): UriInterface
    {
        return $this->uri
            ?? throw new LogicException(message('No uri set'));
    }

    /**
     * @return int<300, 399>
     */
    final public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    protected function data(): array
    {
        return [
            'uri' => $this->uri(),
            'status' => $this->status(),
        ];
    }

    final protected function setUri(UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @param int<300, 399> $status
     */
    final protected function setStatus(int $status): void
    {
        $this->status = $status;
        if (in_array($this->status, static::STATUSES, true)) {
            return;
        }

        throw new InvalidArgumentException(
            message('Invalid status code %status% provided, must be one of %statuses%')
                ->withCode('%status%', strval($this->status))
                ->withCode('%statuses%', implode(', ', static::STATUSES))
        );
    }
}
