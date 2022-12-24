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
use function Chevere\DataStructure\data;
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

    /**
     * @return array<string, mixed>
     * @codeCoverageIgnore
     */
    public function run(): array
    {
        return data(
            uri: $this->uri(),
            status: $this->status(),
        );
    }

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
        if (! in_array($status, static::STATUSES, true)) {
            throw new InvalidArgumentException(
                message('Invalid status code %status% provided, must be one of %statuses%')
                    ->withCode('%status%', strval($status))
                    ->withCode('%statuses%', implode(', ', static::STATUSES))
            );
        }
    }
}
