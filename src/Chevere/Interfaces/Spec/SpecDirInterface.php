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
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of handling a spec path.
 */
interface SpecDirInterface extends ToStringInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(DirInterface $dir);

    /**
     * Returns a child instance for the given `$childPath`.
     *
     * @throws InvalidArgumentException
     */
    public function getChild(string $childPath): SpecDirInterface;
}
