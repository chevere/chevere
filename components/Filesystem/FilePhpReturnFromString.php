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

use Chevere\Exceptions\Filesystem\FileNotPhpException;
use Chevere\Exceptions\Filesystem\PathIsDirException;

/**
 * @codeCoverageIgnore
 */
final class FilePhpReturnFromString extends FilePhpReturn
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
            new FilePhp(
                new FileFromString($path)
            )
        );
    }
}
