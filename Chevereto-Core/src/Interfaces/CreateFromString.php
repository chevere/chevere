<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Interfaces;

/**
 * Provides an interface for objects that can be constructed from a string.
 */
interface CreateFromString
{
    public function createFromString(string $string): CreateFromString;
}
