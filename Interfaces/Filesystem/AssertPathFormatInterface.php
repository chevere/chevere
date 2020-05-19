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

use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;

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
