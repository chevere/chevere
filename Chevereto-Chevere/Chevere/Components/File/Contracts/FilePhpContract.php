<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\File\Contracts;

use Chevere\Components\File\Exceptions\FileNotPhpException;

interface FilePhpContract
{
    public function __construct(FileContract $file);

    /**
     * Provides access to the FileContract instance.
     */
    public function file(): FileContract;
}
