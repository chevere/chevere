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

namespace Chevere\Interfaces\Spec;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Ds\Map;

/**
 * Describes the component in charge of creating a routing spec.
 */
interface SpecMakerInterface
{
    /**
     * @throws FilesystemException
     * @throws InvalidArgumentException
     */
    public function __construct(
        SpecPathInterface $specPath,
        DirInterface $dir,
        RouterInterface $router
    );

    /**
     * Provides access to the generated spec index instance.
     */
    public function specIndex(): SpecIndexInterface;

    /**
     * Provides access to the files map instance.
     */
    public function files(): Map;
}
