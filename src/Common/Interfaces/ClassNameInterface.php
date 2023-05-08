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

namespace Chevere\Common\Interfaces;

use Stringable;

/**
 * Describes the component in charge of handling class name.
 */
interface ClassNameInterface extends Stringable
{
    public function assertInterface(string $class): void;
}
