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

namespace Chevere\Components\Translator;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Translator\TranslatorMakerInterface;
use Gettext\Generator\ArrayGenerator;
use Gettext\Loader\PoLoader;
use Throwable;

final class TranslatorMaker implements TranslatorMakerInterface
{
    private string $locale;

    private DirInterface $localeSourceDir;

    private DirInterface $localeTargetDir;

    private PoLoader $poLoader;

    public function __construct(
        private DirInterface $sourceDir,
        private DirInterface $targetDir
    ) {
        $this->sourceDir->assertExists();
        $this->poLoader = new PoLoader();
    }

    public function sourceDir(): DirInterface
    {
        return $this->sourceDir;
    }

    public function targetDir(): DirInterface
    {
        return $this->targetDir;
    }

    public function withMakeTranslation(string $locale, string $domain): self
    {
        $new = clone $this;
        $new->handleLocale($locale);
        $new->localeSourceDir->assertExists();
        $poFile = new File(
            $new->localeSourceDir->path()->getChild("${domain}.po")
        );
        $poFile->assertExists();

        try {
            $translations = $new->poLoader->loadFile($poFile->path()->toString());
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                previous: $e,
                message: new Message('Unable to load translations.')
            );
        }
        // @codeCoverageIgnoreEnd
        $new->localeTargetDir->createIfNotExists();
        $phpFile = new File(
            $new->localeTargetDir->path()->getChild("${domain}.php")
        );
        $phpFile->removeIfExists();

        try {
            (new ArrayGenerator())
                ->generateFile($translations, $phpFile->path()->toString());
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                previous: $e,
                message: new Message('Unable to generate translations.')
            );
        }
        // @codeCoverageIgnoreEnd
        $phpFile->assertExists();

        return $new;
    }

    private function handleLocale(string $locale): void
    {
        $this->localeSourceDir = $this->sourceDir->getChild($locale . '/');

        try {
            $this->localeSourceDir->assertExists();
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Invalid locale %locale% provided'))
                    ->code('%locale%', $locale)
            );
        }
        $this->localeTargetDir = $this->targetDir->getChild($locale . '/');
        $this->locale = $locale;
    }
}
