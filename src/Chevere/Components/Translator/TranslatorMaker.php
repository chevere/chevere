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
use Chevere\Exceptions\Core\BadMethodCallException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Gettext\Generator\ArrayGenerator;
use Gettext\Loader\PoLoader;
use Throwable;

final class TranslatorMaker
{
    private DirInterface $sourceDir;

    private DirInterface $targetDir;

    private DirInterface $localeSourceDir;

    private DirInterface $localeTargetDir;

    private PoLoader $poLoader;

    private string $locale;

    public function __construct(DirInterface $sourceDir, DirInterface $targetDir)
    {
        try {
            $sourceDir->assertExists();
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: new Message("Source directory doesn't exists")
            );
        }
        $this->sourceDir = $sourceDir;
        $this->targetDir = $targetDir;
        $this->poLoader = new PoLoader();
    }

    public function locale(): string
    {
        $this->assertHasLocale(__METHOD__);

        return $this->locale;
    }

    public function sourceDir(): DirInterface
    {
        return $this->sourceDir;
    }

    public function targetDir(): DirInterface
    {
        return $this->targetDir;
    }

    public function withLocale(string $locale): self
    {
        $new = clone $this;
        $new->localeSourceDir = $new->sourceDir->getChild($locale . '/');

        try {
            $new->localeSourceDir->assertExists();
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Invalid locale %locale% provided'))
                    ->code('%locale%', $locale)
            );
        }
        $new->localeTargetDir = $new->targetDir->getChild($locale . '/');
        $new->locale = $locale;

        return $new;
    }

    public function make(string $domain): void
    {
        $this->assertHasLocale(__METHOD__);
        $this->localeSourceDir->assertExists();
        $poFile = new File(
            $this->localeSourceDir->path()->getChild("${domain}.po")
        );
        $poFile->assertExists();

        try {
            $translations = $this->poLoader->loadFile($poFile->path()->toString());
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        $this->localeTargetDir->createIfNotExists();
        $phpFile = new File(
            $this->localeTargetDir->path()->getChild("${domain}.php")
        );
        $phpFile->removeIfExists();

        try {
            (new ArrayGenerator())
                ->generateFile($translations, $phpFile->path()->toString());
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        $phpFile->assertExists();
    }

    private function assertHasLocale(string $method): void
    {
        if (! isset($this->locale)) {
            throw new BadMethodCallException(
                (new Message('This method requires to define a locale using %method%'))
                    ->code('%method%', $method)
            );
        }
    }
}
