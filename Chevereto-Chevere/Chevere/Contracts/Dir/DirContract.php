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

namespace Chevere\Contracts\Dir;

use Chevere\Contracts\Path\PathContract;

interface DirContract
{
    public function __construct(PathContract $path);

    /**
     * Provides access to the ServicesContract instance.
     */
    public function path(): PathContract;

    /**
     * Returns a boolean indicating whether the directory exists.
     */
    public function exists(): bool;

    /**
     * Creates the directory.
     */
    public function create(): void;

    /**
     * Removes the directory.
     * 
     * @return array An array with all the elements removed
     */
    public function remove(): array;

    /**
     * Removes the contents from a path without deleting the path.
     *
     * @return array An array with all the dir contents removed.
     */
    public function removeContents(): array;
}
