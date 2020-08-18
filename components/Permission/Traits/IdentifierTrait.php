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

namespace Chevere\Components\Permission\Traits;

use ReflectionClass;

trait IdentifierTrait
{
    public function getIdentifier(): string
    {
        return $this->getClassNameIdentifier();
    }

    public function getClassNameIdentifier(): string
    {
        $shortName = (new ReflectionClass(static::class))->getShortName();

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
    }
}
