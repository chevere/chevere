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
use Chevere\Components\Filesystem\FileReturn;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;

final class HtmlOutputterTest extends TestCase
{
    public function testEmpty(): void
    {
        $varDumper = new VarDumper(new HtmlFormatter);
        $outputter = new HtmlOutputter($varDumper);
        $line = __LINE__ - 2;
        // $fileReturn = new FileReturn(
        //     new PhpFile(new File(
        //         new Path(__DIR__ . '/_resources/output-html.php')
        //     ))
        // );
        // $fileReturn->put(new VariableExport($outputter->toString()));
        $parsed = strtr(include '_resources/output-html.php', [
            '%className%' => self::class,
            '%functionName%' => __FUNCTION__,
            '%fileLine%' => __FILE__ . ':' . $line
        ]);
        $this->assertSame($parsed, $outputter->toString());
    }
}
