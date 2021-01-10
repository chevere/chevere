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
use Chevere\Components\Translator\TranslatorInstance;
use Chevere\Components\Translator\TranslatorLoader;
use PHPUnit\Framework\TestCase;

final class TranslatorFunctionsTest extends TestCase
{
    public function testNullTranslator(): void
    {
        $this->assertSame('Language', _t('Language'));
        $username = 'Rodolfo';
        $this->assertSame(
            "${username}'s Images",
            _tf("%s's Images", $username)
        );
        $username = 'Rudy';
        $this->assertSame(
            "${username}'s Images",
            _tt("%s's Images", [
                '%s' => $username,
            ])
        );
        $this->assertSame('image', _n('image', 'images', 1, 1));
        $this->assertSame('2 seconds', _nf('%d second', '%d seconds', 2, 2));
        $value = 123;
        $this->assertSame(
            "${value} seconds",
            _nt('%d second', '%d seconds', $value, [
                '%d' => $value,
            ]));
    }

    public function testTranslator(): void
    {
        $loader = new TranslatorLoader(dirForPath(__DIR__ . '/_resources/compiled/'));
        new TranslatorInstance($loader->getTranslator('es-CL', 'messages'));
        $this->assertSame('Idiomas', _t('Language'));
        $username = 'Rodolfo';
        $this->assertSame(
            "Imágenes de ${username}",
            _tf("%s's Images", $username)
        );
        $username = 'Rudy';
        $this->assertSame(
            "Imágenes de ${username}",
            _tt("%s's Images", [
                '%s' => $username,
            ])
        );
        $this->assertSame('imagen', _n('image', 'images', 1, 1));
        $this->assertSame('2 segundos', _nf('%d second', '%d seconds', 2, 2));
        $value = 123;
        $this->assertSame(
            "${value} segundos",
            _nt('%d second', '%d segundos', $value, [
                '%d' => $value,
            ]));
    }
}
