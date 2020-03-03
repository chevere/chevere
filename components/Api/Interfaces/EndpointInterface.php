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
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Methods\ConnectMethod;
use Chevere\Components\Http\Methods\DeleteMethod;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\HeadMethod;
use Chevere\Components\Http\Methods\OptionsMethod;
use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Http\Methods\TraceMethod;

interface EndpointInterface
{
    const KNOWN_METHODS = [
        'Connect' => ConnectMethod::class,
        'Delete' => DeleteMethod::class,
        'Get' => GetMethod::class,
        'Head' => HeadMethod::class,
        'Options' => OptionsMethod::class,
        'Patch' => PatchMethod::class,
        'Post' => PostMethod::class,
        'Put' => PutMethod::class,
        'Trace' => TraceMethod::class,
    ];

    /**
     * Provides access to the ControllerInterface instance.
     */
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
    public function withRootDir(DirInterface $root): EndpointInterface;
}
