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

namespace Chevere\Components\Writers\Tests;

use Chevere\Components\Writers\StreamWriter;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class StreamWriterTest extends TestCase
{
    public function testConstruct(): void
    {
        $letters = ['Q', 'W', 'E', 'R', 'T', 'Y'];
        $writer = new StreamWriter(stream_for(''));
        foreach ($letters as $letter) {
            $writer->write($letter);
        }
        $this->assertSame(implode('', $letters), $writer->toString());
    }
}
