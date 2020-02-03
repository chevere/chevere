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

namespace Chevere\Components\Filesystem\Interfaces\Path;

use Chevere\Components\Filesystem\Exceptions\Path\PathInvalidException;

interface PathFormatInterface
{
    /**
     * @throws PathInvalidException if the $path format is invalid
     */
    public function __construct(string $path);
}
