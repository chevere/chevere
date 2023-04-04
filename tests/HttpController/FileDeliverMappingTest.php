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

namespace Chevere\Tests\HttpController;

use Chevere\HttpController\FileDeliverMapping;
use Chevere\HttpController\Interfaces\FileDeliveryMapInterface;
use PHPUnit\Framework\TestCase;

final class FileDeliverMappingTest extends TestCase
{
    public function testDefault(): void
    {
        $mapping = new FileDeliverMapping();
        $this->assertSame(FileDeliveryMapInterface::BASENAME, $mapping->basename());
        $this->assertSame(FileDeliveryMapInterface::PATHNAME, $mapping->pathname());
    }

    public function testOptions(): void
    {
        $filename = 'foo';
        $pathname = 'bar';
        $mapping = new FileDeliverMapping($filename, $pathname);
        $this->assertSame($filename, $mapping->basename());
        $this->assertSame($pathname, $mapping->pathname());
    }
}
