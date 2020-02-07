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

use RuntimeException;
use Chevere\Components\Runtime\Sets\SetLocale;
use PHPUnit\Framework\TestCase;

final class SetLocaleTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RuntimeException::class);
        new SetLocale('invalid argument');
    }

    // RuntimeException: The locale functionality is not implemented on your platform, the specified locale es_CL.UTF8 does not exist or the category name LC_ALL is invalid
    // public function testConstruct(): void
    // {
    //     foreach (['es_CL.UTF8', 'en_US.UTF8'] as $val) {
    //         $set = new SetLocale($val);
    //         $this->assertSame('locale', $set->name());
    //         $this->assertSame($val, $set->value());
    //     }
    // }
}
