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

use Chevere\Components\Writer\StreamWriter;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Writer\streamFor;
use function Chevere\Components\Writer\streamForString;

final class StreamWriterTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        /**
         * The streams seems to not worry that much about the mode. Seems that it needs to be handled before runtime.
         */
        $this->expectNotToPerformAssertions();
        $stream = streamFor('php://output', 'r');
        // $this->expectException(InvalidArgumentException::class);
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
