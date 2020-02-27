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

use Chevere\Components\Api\EndpointMethod;

return new class() extends EndpointMethod {
    public function setUp(): void
    {
        $this->setUp = 'thing';
    }

    public function tearDown(): void
    {
        $this->tearDown = 'stuff';
    }

    public function __invoke(): void
    {
        xdd($this);
    }
};
