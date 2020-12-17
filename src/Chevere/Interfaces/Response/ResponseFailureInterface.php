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
 * Describes the component in charge of defining a failure controller response.
 */
interface ResponseFailureInterface extends ResponseInterface
{
    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     */
    public function withData(array $data): self;
}
