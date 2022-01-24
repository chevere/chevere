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

namespace Chevere\Tests\src;

use Chevere\Filesystem\Dir;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class DirHelper
{
    private DirInterface $dir;

    public function __construct(TestCase $object)
    {
        $reflection = new ReflectionObject($object);
        $dir = dirname($reflection->getFileName());
        $shortName = $reflection->getShortName();
        $this->dir = new Dir(new Path("${dir}/_resources/${shortName}/"));
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }
}
