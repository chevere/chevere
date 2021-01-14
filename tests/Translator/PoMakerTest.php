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

namespace Chevere\Tests\Translator;

use BadMethodCallException;
use function Chevere\Components\Filesystem\dirForPath;
use function Chevere\Components\Filesystem\fileForPath;
use Chevere\Components\Translator\PoMaker;
use PHPUnit\Framework\TestCase;

final class PoMakerTest extends TestCase
{
    public function testMakeWithoutScanner(): void
    {
        $this->expectException(BadMethodCallException::class);
        (new PoMaker('en-US', 'messages'))
            ->make(dirForPath(__DIR__ . '/'));
    }

    public function testMakePo(): void
    {
        $locale = 'en-US';
        $poFile = fileForPath(__DIR__ . "/_resources/make/${locale}/messages.po");
        $poFile->removeIfExists();
        $dir = dirForPath(__DIR__ . '/_resources/');
        $poMaker = (new PoMaker($locale, 'messages'))
            ->withScanFor($dir->getChild('user/'));
        $poMaker->make($dir->getChild('make/'));
        $this->assertFileExists($poFile->path()->toString());
    }
}
