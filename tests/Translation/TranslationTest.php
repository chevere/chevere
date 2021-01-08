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

use Gettext\Generator\ArrayGenerator;
use Gettext\Loader\PoLoader;
use Gettext\Translator;
use PHPUnit\Framework\TestCase;

final class TranslationTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        $name = 'es-CL';
        $translations = (new PoLoader())
            ->loadFile(__DIR__ . "/_resources/locales/${name}/messages.po");
        (new ArrayGenerator())
            ->generateFile($translations, __DIR__ . "/_resources/array/${name}.php");
    }

    public function testTranslate(): void
    {
        $name = 'es-CL';
        $translator = (new Translator())
            ->loadTranslations(
            __DIR__ . "/_resources/array/${name}.php"
        );
        $this->assertSame('Idiomas', $translator->gettext('Language'));
        $this->assertSame('imágenes', $translator->ngettext('image', 'images', 2));
    }
}
