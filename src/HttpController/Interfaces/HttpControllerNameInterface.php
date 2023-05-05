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

namespace Chevere\HttpController\Interfaces;

use Stringable;

/**
 * Describes the component in charge of doing.
 */
interface HttpControllerNameInterface extends Stringable
{
    /**
     * @return class-string HttpControllerInterface
     */
    public function __toString(): string;
}
