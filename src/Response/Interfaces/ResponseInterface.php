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

namespace Chevere\Response\Interfaces;

/**
 * Describes the component in charge of defining a success response.
 */
interface ResponseInterface
{
    public const TOKEN_LENGTH = 256;

    /**
     * Return an instance with the specified code.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified code.
     */
    public function withCode(int $code): self;

    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     *
     * @param mixed $value Named arguments for response data (name to data key)
     */
    public function withData(mixed ...$value): self;

    /**
     * Provides access to uuid (v4).
     */
    public function uuid(): string;

    /**
     * Provides access to token.
     */
    public function token(): string;

    /**
     * Provides access to data.
     *
     * @return array<string, mixed>
     */
    public function data(): array;

    /**
     * Provides access to code.
     */
    public function code(): int;
}
