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

use Chevere\Common\Interfaces\AttributesInterface;

/**
 * Describes the component in charge of defining a success response.
 */
interface ResponseInterface extends AttributesInterface
{
    public function __construct(mixed ...$namedData);

    /**
     * Return an instance with the specified status.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified status.
     */
    public function withStatus(int $code): self;

    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     *
     * @param mixed $namedData Named arguments for response data (name to data key)
     */
    public function withData(mixed ...$namedData): self;

    /**
     * Provides access to uuid.
     */
    public function uuid(): string;

    /**
     * Provides access to token.
     */
    public function token(): string;

    /**
     * Provides access to data.
     */
    public function data(): array;

    /**
     * Provides access to status.
     */
    public function status(): int;
}
