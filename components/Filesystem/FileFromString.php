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

use Chevere\Components\Filesystem\Exceptions\PathIsDirException;

/**
 * @codeCoverageIgnore
 */
final class FileFromString extends File
{
    /**
     * @var string $path Absolute file path.
     * @throws PathIsDirException if the $path represents a directory
     */
    public function __construct(string $path)
    {
        parent::__construct(new Path($path));
    }
}
