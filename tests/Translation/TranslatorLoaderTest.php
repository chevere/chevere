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
use Chevere\Components\Translation\TranslatorLoader;
use Chevere\Exceptions\Core\DomainException;
use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Gettext\Translator;
use PHPUnit\Framework\TestCase;

final class TranslatorLoaderTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(DirNotExistsException::class);
        $loader = new TranslatorLoader(
            dirForPath(__DIR__ . '/404/')
        );
    }

    public function testConstruct(): void
    {
        $dir = dirForPath(__DIR__ . '/_resources/compiled/');
        $loader = new TranslatorLoader($dir);
        $this->assertSame($dir, $loader->dir());
    }

    public function testGetTranslatorMissingDir(): void
    {
        $loader = $this->getTranslationLoad();
        $this->expectException(DirNotExistsException::class);
        $loader->getTranslator('es-404', 'messages');
    }

    public function testGetTranslatorMissingFile(): void
    {
        $loader = $this->getTranslationLoad();
        $this->expectException(FileNotExistsException::class);
        $loader->getTranslator('es-CL', '404');
    }

    public function testGetTranslatorInvalidFile(): void
    {
        $loader = $this->getTranslationLoad();
        $this->expectException(DomainException::class);
        $loader->getTranslator('es-CL', 'invalid');
    }

    public function testGetTranslator(): void
    {
        $loader = $this->getTranslationLoad();
        $this->assertInstanceOf(
            Translator::class,
            $loader->getTranslator('es-CL', 'messages')
        );
    }

    private function getTranslationLoad(): TranslatorLoader
    {
        return new TranslatorLoader(
            dirForPath(__DIR__ . '/_resources/compiled/')
        );
    }
}
