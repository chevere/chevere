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

use Chevere\Components\Filesystem\Exceptions\FileNotPhpException;
use Chevere\Components\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Components\Filesystem\Exceptions\PathIsDirException;
use Chevere\Components\Filesystem\Exceptions\PathNotAbsoluteException;

final class FilePhpFromString extends FilePhp
{
    /**
     * @var string $path Absolute file path.
     * @throws PathDotSlashException
     * @throws PathDoubleDotsDashException
     * @throws PathExtraSlashesException
     * @throws PathNotAbsoluteException
     * @throws PathIsDirException
     * @throws FileNotPhpException
     */
    public function __construct(string $path)
    {
        parent::__construct(
            new FileFromString($path)
        );
    }
}
