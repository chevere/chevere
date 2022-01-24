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

namespace Chevere\Tests\VarDump;

use Chevere\Throwable\Exceptions\LogicException;
use function Chevere\VarDump\varDumpConsole;
use Chevere\VarDump\VarDumpInstance;
use PHPUnit\Framework\TestCase;

final class VarDumpInstanceTest extends TestCase
{
    public function testNoConstruct(): void
    {
        $this->expectException(LogicException::class);
        VarDumpInstance::get();
    }

    public function testConstruct(): void
    {
        $varDump = varDumpConsole();
        $instance = new VarDumpInstance($varDump);
        $this->assertSame($varDump, $instance::get());
    }
}
