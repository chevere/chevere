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

namespace Chevere\Components\Router\Interfaces;

use Throwable;

interface RouterPropertyInterface
{
    /**
     * Asserts the property.
     *
     * @throws Throwable describing any kind of error
     */
    public function assert(): void;
}
