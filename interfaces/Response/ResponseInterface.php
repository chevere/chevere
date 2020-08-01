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
    public function __construct(array $data);

    /**
     * Provides access to response data.
     */
    public function data(): array;
}
