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

namespace Chevere\Components\VarDump\Tests\Processors;

use Chevere\Components\VarDump\Processors\StringProcessor;
use Chevere\Components\VarDump\Tests\Processors\Traits\VarProcessTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StringProcessorTest extends TestCase
{
    use VarProcessTrait;

    public function testConstruct(): void
    {
        foreach (['', 'string', 'cádena', 'another string', '100', 'false'] as $var) {
            $processor = new StringProcessor($this->getVarProcess($var));
            $info = 'length=' . mb_strlen($var);
            $string = "string $var ($info)";
            $this->assertSame($info, $processor->info(), "info:$var");
            $this->assertSame($string, $this->getWriter()->toString(), "string:$var");
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StringProcessor($this->getVarProcess(null));
    }
}
