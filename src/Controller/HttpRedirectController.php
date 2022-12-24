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
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\objectParameter;
use function Chevere\Parameter\parameters;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Psr\Http\Message\UriInterface;

abstract class HttpRedirectController extends HttpController implements HttpRedirectControllerInterface
{
    private ?UriInterface $uri;

    private int $status = 302;

    final public function getResponseParameters(): ParametersInterface
    {
        return parameters(
            uri: objectParameter(UriInterface::class),
            status: integerParameter()
                ->withAccept(...static::STATUSES),
        );
    }

    final public function withUri(UriInterface $uri): static
    {
        $new = clone $this;
        $new->setUri($uri);

        return $new;
    }

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

    final public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, UriInterface|int>
     */
    final protected function data(): array
    {
        return [
            'uri' => $this->uri(),
            'status' => $this->status(),
        ];
    }

    final protected function assertStatus(): void
    {
        if (! in_array($this->status, static::STATUSES, true)) {
            throw new InvalidArgumentException(
                message('Invalid status code %status% provided, must be one of %statuses%')
                    ->withCode('%status%', strval($this->status))
                    ->withCode('%statuses%', implode(', ', static::STATUSES))
            );
        }
    }

    final protected function setUri(UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    final protected function setStatus(int $status): void
    {
        $this->status = $status;
        $this->assertStatus();
    }
}
