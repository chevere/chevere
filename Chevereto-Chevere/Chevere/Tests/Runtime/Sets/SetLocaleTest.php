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

use Chevere\Components\Runtime\Exceptions\RuntimeException;
use Chevere\Components\Runtime\Sets\SetLocale;
use PHPUnit\Framework\TestCase;

final class SetLocaleTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RuntimeException::class);
        new SetLocale('fakeLocale');
    }

    public function testConstruct(): void
    {
        foreach (['es_CL.UTF8', 'en_US.UTF8'] as $val) {
            $set = new SetLocale($val);
            $this->assertSame('locale', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
