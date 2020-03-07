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

namespace Chevere\Components\Iterators\Interfaces;

use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use RecursiveIteratorIterator;
use SplObjectStorage;

interface RoutePathIteratorInterface
{
    const ROUTE_DECORATOR_BASENAME = 'RouteDecorator.php';

    public function __construct(DirInterface $dir);

    public function recursiveIterator(): RecursiveIteratorIterator;

    /**
     * Provides access to the SplObjectStorage instance.
     *
     * @return SplObjectStorage RoutePathInterface objects, with RouteDecorator as data (getInfo)
     */
    public function objects(): SplObjectStorage;
}
