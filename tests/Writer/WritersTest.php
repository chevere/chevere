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

use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Writer\StreamWriter;
use Chevere\Writer\Writers;
use PHPUnit\Framework\TestCase;
use function Chevere\Writer\streamTemp;

final class WritersTest extends TestCase
{
    public function testConstruct()
    {
        $writers = new Writers();
        foreach (['output', 'error', 'debug', 'log'] as $fnName) {
            $this->assertInstanceOf(
                WriterInterface::class,
                $writers->{$fnName}()
            );
        }
    }

    public function testWith(): void
    {
        $writer = new StreamWriter(streamTemp(''));
        $writers = new Writers();
        $writersWith = $writers->with($writer);
        $this->assertNotSame($writers, $writersWith);
        foreach (['output', 'error', 'debug', 'log'] as $name) {
            $this->assertSame($writer, $writersWith->{$name}());
        }
    }

    public function testWithX(): void
    {
        foreach (['output', 'error', 'debug', 'log'] as $name) {
            $writer = new StreamWriter(streamTemp(''));
            $withFn = 'with' . ucfirst($name);
            $writers = new Writers();
            $writersWithX = $writers->{$withFn}($writer);
            $this->assertNotSame($writers, $writersWithX);
            $this->assertSame($writer, $writersWithX->{$name}());
        }
    }
}
