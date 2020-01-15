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

namespace Chevere\Components\VarDump\Tests;

use Chevere\Components\VarDump\Dumpeable;
use PHPUnit\Framework\TestCase;

final class DumpeableTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        $variableDump = new Dumpeable('');
        // var_dump($variableDump);
        // xdd($variableDump);
    }
}
