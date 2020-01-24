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

namespace Chevere\Components\Runtime\Tests\Sets;

use InvalidArgumentException;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use PHPUnit\Framework\TestCase;

/**
 * @requires extension mbstring
 */
final class SetDefaultCharsetTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetDefaultCharset('invalid argument');
    }

    public function testConstruct(): void
    {
        foreach (['UTF-8', 'ISO-8859-1'] as $pos => $val) {
            $set = new SetDefaultCharset($val);
            $this->assertSame('defaultCharset', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
