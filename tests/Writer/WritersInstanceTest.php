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

namespace Chevere\Tests\Writer;

use Chevere\Components\Writer\Writers;
use Chevere\Components\Writer\WritersInstance;
use Chevere\Exceptions\Core\LogicException;
use PHPUnit\Framework\TestCase;

final class WritersInstanceTest extends TestCase
{
    public function testNoConstruct(): void
    {
        $this->expectException(LogicException::class);
        WritersInstance::get();
    }

    public function testConstruct(): void
    {
        $writers = new Writers();
        $instance = new WritersInstance($writers);
        $this->assertSame($writers, $instance::get());
    }
}
