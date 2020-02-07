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
use Chevere\Components\Runtime\Sets\SetUriScheme;
use PHPUnit\Framework\TestCase;

final class SetUriSchemeTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetUriScheme('invalid argument');
    }

    public function testConstruct(): void
    {
        foreach (['http', 'https'] as $val) {
            $set = new SetUriScheme($val);
            $this->assertSame('uriScheme', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
