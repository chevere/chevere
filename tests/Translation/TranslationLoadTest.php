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

namespace Chevere\Tests\Translation;

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Translation\TranslationLoad;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Gettext\Translator;
use PHPUnit\Framework\TestCase;

final class TranslationLoadTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $translationLoad = new TranslationLoad(
            dirForPath(__DIR__ . '/404/')
        );
    }

    public function testConstruct(): void
    {
        $dir = dirForPath(__DIR__ . '/_resources/compiled/');
        $translationLoad = new TranslationLoad($dir);
        $this->assertSame($dir, $translationLoad->dir());
    }

    public function testGetTranslatorInvalidDir(): void
    {
        $translationLoad = $this->getTranslationLoad();
        $this->expectException(DirNotExistsException::class);
        $translationLoad->getTranslator('es-404', 'messages');
    }

    public function testGetTranslatorInvalidFile(): void
    {
        $translationLoad = $this->getTranslationLoad();
        $this->expectException(FileNotExistsException::class);
        $translationLoad->getTranslator('es-CL', '404');
    }

    public function testGetTranslator(): void
    {
        $translationLoad = $this->getTranslationLoad();
        $this->assertInstanceOf(Translator::class, $translationLoad->getTranslator('es-CL', 'messages'));
    }

    private function getTranslationLoad(): TranslationLoad
    {
        return new TranslationLoad(
            dirForPath(__DIR__ . '/_resources/compiled/')
        );
    }
}
