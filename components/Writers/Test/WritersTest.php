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

namespace Chevere\Components\Writer\Tests;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\Writers\Writers;
use Exception;
use PHPUnit\Framework\TestCase;

final class WritersTest extends TestCase
{
    public function testConstruct(): void
    {
        // xdd(new Path(__DIR__));
    }
}
