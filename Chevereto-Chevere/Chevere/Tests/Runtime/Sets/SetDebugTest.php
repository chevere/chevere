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
use Chevere\Components\Runtime\Sets\SetDebug;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

final class SetDebugTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetDebug('');
    }

    public function testConstruct(): void
    {
        foreach (['0', '1'] as $pos => $val) {
            $debug = new SetDebug($val);
            $this->assertSame('debug', $debug->name());
            $this->assertSame($val, $debug->value());
        }
    }
}
