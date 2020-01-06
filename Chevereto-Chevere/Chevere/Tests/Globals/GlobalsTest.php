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

namespace Chevere\Tests\Globals;

use Chevere\Components\Globals\Exceptions\GlobalsKeyException;
use Chevere\Components\Globals\Exceptions\GlobalsTypeException;
use Chevere\Components\Globals\Globals;
use Chevere\Contracts\Globals\GlobalsContract;
use PHPUnit\Framework\TestCase;

final class GlobalsTest extends TestCase
{
    public function testBadGlobalKeysConstruct(): void
    {
        $this->expectException(GlobalsKeyException::class);
        new Globals([]);
    }

    public function testBadGlobalTypesConstruct(): void
    {
        $this->expectException(GlobalsTypeException::class);
        $globs = [
            'argc' => null,
            'argv' => null,
            '_SERVER' => null,
            '_GET' => null,
            '_POST' => null,
            '_FILES' => null,
            '_COOKIE' => null,
            '_SESSION' => null,
            '_REQUEST' => null,
            '_ENV' => null,
        ];
        new Globals($globs);
    }

    public function testGlobalTypesConstruct(): void
    {
        $globs = [
            'argc' => 0,
            'argv' => [],
            '_SERVER' => [],
            '_GET' => [],
            '_POST' => [],
            '_FILES' => [],
            '_COOKIE' => [],
            '_SESSION' => [],
            '_REQUEST' => '',
            '_ENV' => [],
        ];
        $globals = new Globals($globs);
        $this->assertSame($globs, $globals->globals());
        foreach (GlobalsContract::KEYS as $pos => $key) {
            $method = GlobalsContract::METHODS[$pos];
            $this->assertSame($globs[$key], $globals->$method());
        }
    }

    public function testGlobalConstruct(): void
    {
        $globals = new Globals($GLOBALS);
        foreach (GlobalsContract::KEYS as $pos => $key) {
            $method = GlobalsContract::METHODS[$pos];
            $this->assertSame($GLOBALS[$key], $globals->$method());
        }
    }
}
