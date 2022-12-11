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

namespace Chevere\Tests\VariableSupport\_resources;

use Chevere\Filesystem\File;

final class ClassWithPropertyNotExportable
{
    public function __construct(
        private File $file
    ) {
    }

    public static function __set_state($state)
    {
        return new self($state);
    }
}
