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

namespace Chevere\Components\Spec;

use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Spec\SpecDirInterface;

final class SpecDir implements SpecDirInterface
{
    private DirInterface $dir;

    public function __construct(DirInterface $dir)
    {
        $this->dir = $dir;
    }

    public function toString(): string
    {
        return $this->dir->path()->absolute();
    }

    public function getChild(string $childPath): SpecDirInterface
    {
        return new self($this->dir->getChild($childPath));
    }
}
