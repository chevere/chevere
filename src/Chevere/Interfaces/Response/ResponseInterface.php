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

use Chevere\Interfaces\Parameter\ParametersInterface;

/**
 * Describes the component in charge of handling the response.
 */
interface ResponseInterface
{
    public function __construct(ParametersInterface $parameters, array $data);

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
