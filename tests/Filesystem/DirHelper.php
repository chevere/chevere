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

namespace Chevere\Tests\Filesystem;

use Chevere\Filesystem\Directory;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class DirHelper
{
    private DirectoryInterface $directory;

    public function __construct(TestCase $object)
    {
        $reflection = new ReflectionObject($object);
        $directory = dirname($reflection->getFileName());
        $shortName = $reflection->getShortName();
        $this->directory = new Directory(new Path("{$directory}/_resources/{$shortName}/"));
    }

    public function directory(): DirectoryInterface
    {
        return $this->directory;
    }
}
