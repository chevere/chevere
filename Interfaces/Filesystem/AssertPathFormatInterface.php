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

namespace Chevere\Interfaces\Filesystem;

use Chevere\Components\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Components\Filesystem\Exceptions\PathNotAbsoluteException;

interface AssertPathFormatInterface
{
    /**
     * @throws PathNotAbsoluteException
     * @throws PathDoubleDotsDashException
     * @throws PathDotSlashException
     * @throws PathExtraSlashesException
     */
    public function __construct(string $path);
}
