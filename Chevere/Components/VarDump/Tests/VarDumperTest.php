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

use Chevere\Components\Filesystem\File\Exceptions\FileNotFoundException;
use Chevere\Components\Filesystem\File\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\FileReturn;
use Chevere\Components\Filesystem\Path\Path;
use Chevere\Components\VarDump\Dumpers\ConsoleDumper;
use Chevere\Components\VarDump\Dumpers\HtmlDumper;
use Chevere\Components\VarDump\Dumpers\PlainDumper;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumperTest extends TestCase
{
    private function getVars(): array
    {
        return [null, true, 1, '', [], new stdClass];
    }

    private function getDumpers(): array
    {
        return [];
    }

    // private function createResources(): void
    // {
    //     $dumpers = $this->getDumpers();
    //     foreach ($dumpers as $shortName => $dumper) {
    //         $file = new File(
    //             new Path(
    //                 __DIR__ . '/' .
    //                 sprintf('resources/%s-dumped.php', $shortName)
    //             )
    //         );
    //         try {
    //             $file->remove();
    //         } catch (FileNotFoundException | FileUnableToRemoveException $e) {
    //             // $e silence
    //         }
    //         $file->create();
    //         $fr =
    //             new FileReturn(
    //                 new FilePhp($file)
    //             );
    //         $dumper = $dumper->withVars(...$this->getVars());
    //         $fr->put(new VariableExport($dumper->outputter()->toString()));
    //     }
    // }

    public function testConstruct(): void
    {
        $formatter = new PlainFormatter;
        $varDumper = new VarDumper($formatter);
        $this->assertSame($formatter, $varDumper->formatter());
        $this->assertSame([], $varDumper->vars());
        // xdd($varDumper->debugBacktrace());
    }

    // public function testXdd(): void
    // {
    //     xdd(...$this->getVars());
    // }
}
