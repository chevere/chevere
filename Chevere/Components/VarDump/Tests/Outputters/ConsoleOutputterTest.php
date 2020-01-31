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

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\FileReturn;
use Chevere\Components\Filesystem\Path\Path;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;

final class ConsoleOutputterTest extends TestCase
{
    public function testEmpty(): void
    {
        $varDumper = new VarDumper(new ConsoleFormatter);
        $outputter = new ConsoleOutputter($varDumper);
        $line = __LINE__ - 2;
        // $fileReturn = new FileReturn(
        //     new PhpFile(new File(
        //         new Path(__DIR__ . '/resources/output-console.php')
        //     ))
        // );
        // $fileReturn->put(new VariableExport($outputter->toString()));
        $parsed = strtr(include 'resources/output-console.php', [
            '%className%' => self::class,
            '%functionName%' => __FUNCTION__,
            '%fileLine%' => __FILE__ . ':' . $line
        ]);
        $this->assertSame($parsed, $outputter->toString());
    }
}
