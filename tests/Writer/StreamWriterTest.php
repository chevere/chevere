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

use function Chevere\Components\Writer\streamFor;
use function Chevere\Components\Writer\streamForString;
use Chevere\Components\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;

final class StreamWriterTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectNotToPerformAssertions();
        $stream = streamFor('php://output', 'r');
        new StreamWriter($stream);
    }

    public function testWrite(): void
    {
        $letters = ['Q', 'W', 'E', 'R', 'T', 'Y'];
        $writer = new StreamWriter(streamForString(''));
        foreach ($letters as $letter) {
            $writer->write($letter);
        }
        $this->assertSame(implode('', $letters), $writer->toString());
    }
}
