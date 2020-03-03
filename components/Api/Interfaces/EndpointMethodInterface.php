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

namespace Chevere\Components\Api\Interfaces;

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;

interface EndpointMethodInterface
{
    public function controller(): ControllerInterface;

    /**
     * Provides access to the absolute path to the class file.
     */
    public function whereIs(): string;

    /**
     * Provides access to the MethodInterface instance.
     */
    public function method(): MethodInterface;

    /**
     * Return an instance with the specified root DirInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified root DirInterface.
     */
    public function withRootDir(DirInterface $root): EndpointMethodInterface;
}
