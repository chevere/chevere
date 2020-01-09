<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Runtime\Sets;

use Chevere\Components\Runtime\Exceptions\InvalidArgumentException;
use Chevere\Components\Runtime\Exceptions\RuntimeException;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use Chevere\Components\Stopwatch\Stopwatch;
use PHPUnit\Framework\TestCase;

final class SetDefaultCharsetTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetDefaultCharset('fakeCharset');
    }

    public function testConstruct(): void
    {
        foreach (['UTF-8', 'ISO-8859-1'] as $pos => $val) {
            $debug = new SetDefaultCharset($val);
            $this->assertSame('defaultCharset', $debug->name());
            $this->assertSame($val, $debug->value());
        }
    }
}
