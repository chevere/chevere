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

namespace Chevere\Tests\VarDump;

use Chevere\Components\VarDump\Dumpers\HtmlDumper;
use Chevere\Components\VarDump\Dumpers\PlainDumper;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DumperTest extends TestCase
{
    private function getVars(): array
    {
        return [null, true, 1, '', [], new stdClass];
    }

    public function testDumpers(): void
    {
        $dumpers = [
            new PlainDumper(),
            new HtmlDumper(),
        ];
        foreach ($dumpers as $pos => $dumper) {
            $vars = $this->getVars();
            ob_start();
            $dumper->dump(...$vars);
            $buffer = ob_get_contents();
            ob_end_clean();
            $this->assertTrue(strlen($buffer) > 0);
            $this->assertSame($vars, $dumper->vars());
        }
        // Note: Console dumper can't be tested here
    }
}
