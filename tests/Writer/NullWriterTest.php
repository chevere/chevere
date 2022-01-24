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

use Chevere\Writer\NullWriter;
use PHPUnit\Framework\TestCase;

final class NullWriterTest extends TestCase
{
    public function testConstruct(): void
    {
        $letters = ['Q', 'W', 'E', 'R', 'T', 'Y'];
        $writer = new NullWriter();
        foreach ($letters as $letter) {
            $writer->write($letter);
        }
        $this->assertSame('', $writer->__toString());
    }
}
