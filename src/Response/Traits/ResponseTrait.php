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

namespace Chevere\Response\Traits;

use Chevere\Response\Interfaces\ResponseInterface;
use Ramsey\Uuid\Uuid;

trait ResponseTrait
{
    private string $uuid;

    private string $token;

    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    private int $code = 0;

    public function __construct(mixed ...$value)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->token = bin2hex(random_bytes(ResponseInterface::TOKEN_LENGTH / 2));
        /** @var array<string, mixed> $value */
        $this->data = $value;
    }

    public function withCode(int $code): static
    {
        $new = clone $this;
        $new->code = $code;

        return $new;
    }

    public function withData(mixed ...$value): static
    {
        $new = clone $this;
        /** @var array<string, mixed> $value */
        $new->data = $value;

        return $new;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function code(): int
    {
        return $this->code;
    }
}
