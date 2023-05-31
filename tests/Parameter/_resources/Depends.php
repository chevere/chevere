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

namespace Chevere\Tests\Parameter\_resources;

use Chevere\Attribute\StringAttribute;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\FileInterface;

final class Depends
{
    public function useObject(FileInterface $file)
    {
    }

    public function useString(
        #[StringAttribute(regex: '/^[a-z]$/', description: 'A string')]
        string $string = 'default'
    ) {
    }

    public function useUnion(string|int $union)
    {
    }

    // PHP 8.1
    // public function useIntersection(FileInterface&DirectoryInterface $intersection)
    // {
    // }
}