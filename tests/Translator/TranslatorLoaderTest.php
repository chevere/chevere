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

use function Chevere\Filesystem\dirForPath;
use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Translator\Interfaces\TranslatorLoaderInterface;
use Chevere\Translator\TranslatorLoader;
use DomainException;
use Gettext\Translator;
use InvalidArgumentException;
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

    public function testGetTranslatorInvalidLocale(): void
    {
        $loader = $this->getTranslationLoad();
        $this->expectException(InvalidArgumentException::class);
        $loader->getTranslator('es-404', 'messages');
    }

    public function testGetTranslatorInvalidDomain(): void
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

    private function getTranslationLoad(): TranslatorLoaderInterface
    {
        return new TranslatorLoader(
            dirForPath(__DIR__ . '/_resources/compiled/')
        );
    }
}
