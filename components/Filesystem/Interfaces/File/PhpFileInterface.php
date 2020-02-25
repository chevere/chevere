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

namespace Chevere\Components\Filesystem\Interfaces\File;

interface PhpFileInterface
{
    /**
     * @throws FileNotPhpException if $file doesn't represent a PHP filepath.
     */
    public function __construct(FileInterface $file);

    /**
     * Provides access to the FileInterface instance.
     */
    public function file(): FileInterface;

    // public function isCompileable(): bool;

    public function cache(): void;
}
