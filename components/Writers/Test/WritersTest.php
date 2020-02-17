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

use Chevere\Components\Writers\Interfaces\WriterInterface;
use Chevere\Components\Writers\StreamWriter;
use Chevere\Components\Writers\Writers;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class WritersTest extends TestCase
{
    public function testConstruct()
    {
        $writers = new Writers();
        foreach (['out', 'error', 'debug', 'log'] as $fnName) {
            $this->assertInstanceOf(
                WriterInterface::class,
                $writers->$fnName()
            );
        }
    }

    public function testWith(): void
    {
        foreach (['out', 'error', 'debug', 'log']  as $name) {
            $writer = new StreamWriter(stream_for(''));
            $withFn = 'with' . ucfirst($name);
            $writers = (new Writers())->$withFn($writer);
            $this->assertSame($writer, $writers->$name());
        }
    }
}
