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

namespace Chevere\Components\Filesystem;

use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;

/**
 * @codeCoverageIgnore
 */
final class DirFromString extends Dir
{
    /**
     * @var string $path Absolute dir path.
     * @throws PathDotSlashException
     * @throws PathDoubleDotsDashException
     * @throws PathExtraSlashesException
     * @throws PathNotAbsoluteException
     */
    public function __construct(string $path)
    {
        parent::__construct(new Path($path));
    }
}
