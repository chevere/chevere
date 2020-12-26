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

namespace Chevere\Interfaces\Response;

/**
 * Describes the component in charge of handling the response.
 */
interface ResponseInterface
{
    /**
     * @param mixed $data Named arguments for response data (name to data key)
     */
    public function __construct(mixed ...$data);

    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     *
     * @param mixed $data Named arguments for response data (name to data key)
     */
    public function withData(mixed ...$data): self;

    /**
     * Provides access to response uuid.
     */
    public function uuid(): string;

    /**
     * Provides access to response token.
     */
    public function token(): string;

    /**
     * Provides access to response data.
     */
    public function data(): array;
}
