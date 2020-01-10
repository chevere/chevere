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

use Chevere\Components\VarDump\Dumpers\PlainDumper;
use PHPUnit\Framework\TestCase;

final class DumperTest extends TestCase
{
    public function testWea(): void
    {
        $dumper = new PlainDumper();
        ob_start();
        $dumper->dump('Daniel Stringo');
        $buffered = ob_get_contents();
        ob_end_clean();
        $echo = '`' . $buffered . '`';
        // echo $echo;
        xdd('Daniel Stringo');
    }
}
