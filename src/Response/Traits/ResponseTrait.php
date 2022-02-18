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

use Chevere\Common\Traits\AttributesTrait;
use Ramsey\Uuid\Uuid;

trait ResponseTrait
{
    use AttributesTrait;

    private string $uuid;

    private string $token;

    private array $data = [];

    private int $code = 0;

    public function __construct(mixed ...$namedData)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->token = bin2hex(random_bytes(128));
        $this->data = $namedData;
    }

    public function withCode(int $code): static
    {
        $new = clone $this;
        $new->code = $code;

        return $new;
    }

    public function withData(mixed ...$namedData): static
    {
        $new = clone $this;
        $new->data = $namedData;

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
