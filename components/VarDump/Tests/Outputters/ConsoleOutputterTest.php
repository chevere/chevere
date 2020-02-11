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
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Str\Str;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Variable\VariableExport;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use PHPUnit\Framework\TestCase;

final class ConsoleOutputterTest extends TestCase
{
    public function testEmpty(): void
    {
        $varDumper = new VarDumper(new PlainFormatter);
        $outputter = new ConsoleOutputter($varDumper);
        $line = __LINE__ - 2;
        // $fileReturn = new FileReturn(
        //     new PhpFile(new File(
        //         new Path(__DIR__ . '/_resources/output-console.php')
        //     ))
        // );
        // $fileReturn->put(new VariableExport($outputter->toString()));
        $expected = include '_resources/output-console-color.php';
        if ((new ConsoleColor())->isSupported() === false) {
            $expected = (new Str($expected))->stripANSIColors();
        }
        $parsed = strtr($expected, [
            '%varDumperClassName%' => VarDumper::class,
            '%className%' => self::class,
            '%functionName%' => __FUNCTION__,
            '%fileLine%' => __FILE__ . ':' . $line
        ]);
        $this->assertSame($parsed, $outputter->toString());
    }
}
