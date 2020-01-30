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

namespace Chevere\Components\VarDump\Tests;

use Chevere\Components\VarDump\VarDump;
use PHPUnit\Framework\TestCase;

final class VarDumpTest extends TestCase
{
    public function testRuntime(): void
    {
        $this->expectNotToPerformAssertions();
        (new VarDump('string'))->toString();
    }
}
