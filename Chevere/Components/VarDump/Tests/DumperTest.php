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

use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Path\Path;
use Chevere\Components\VarDump\Dumpers\ConsoleDumper;
use Chevere\Components\VarDump\Dumpers\HtmlDumper;
use Chevere\Components\VarDump\Dumpers\PlainDumper;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DumperTest extends TestCase
{
    private function getVars(): array
    {
        return [null, true, 1, '', [], new stdClass];
    }

    private function getDumpers(): array
    {
        return [
            // 'PlainDumper' => new PlainDumper(),
            'ConsoleDumper' => new ConsoleDumper(),
            // 'HtmlDumper' => new HtmlDumper(),
        ];
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

    public function testDumpers(): void
    {
        // $this->createResources();
        $dumpers = $this->getDumpers();
        foreach ($dumpers as $shortName => $dumper) {
            $vars = $this->getVars();
            $dumper = $dumper->withVars(...$vars);
            $this->assertSame($vars, $dumper->vars());
            $disk = include sprintf('resources/%s-dumped.php', $shortName);
            $line = $dumper->debugBacktrace()[0]['line'];
            $fixed = str_replace('%fileLine%', __FILE__ . ':' . $line, $disk);
            $this->assertSame($fixed, $dumper->toString());
        }
        // Note: Console dumper can't be tested here
    }

    // public function testXdd(): void
    // {
    //     xdd(...$this->getVars());
    // }
}
