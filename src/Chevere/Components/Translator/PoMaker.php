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
use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Components\Iterator\RecursiveFileFilterIterator;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\BadMethodCallException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Gettext\Generator\PoGenerator;
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class PoMaker
{
    public const FUNCTIONS = [
        '__' => 'gettext',
        '__f' => 'gettext',
        '__t' => 'gettext',
        '__n' => 'ngettext',
        '__nf' => 'ngettext',
        '__nt' => 'ngettext',
    ];

    private string $locale;

    private string $domain;

    private DirInterface $sourceDir;

    private PhpScanner $phpScanner;

    public function __construct(string $locale, string $domain)
    {
        $this->locale = $locale;
        $this->domain = $domain;
    }

    public function withScannerFor(DirInterface $sourceDir): self
    {
        $new = clone $this;
        $sourceDir->assertExists();
        $new->sourceDir = $sourceDir;
        $new->phpScanner = new PhpScanner(Translations::create($new->domain));
        $new->phpScanner->setDefaultDomain($new->domain);
        $new->phpScanner->setFunctions(self::FUNCTIONS);
        $iterator = $new->getIterator();
        $iterator->rewind();
        while ($iterator->valid()) {
            $pathName = $iterator->current()->getPathName();
            $new->phpScanner->scanFile($pathName);
            $iterator->next();
        }

        return $new;
    }

    public function makeAt(DirInterface $targetDir): void
    {
        if (! isset($this->phpScanner)) {
            throw new BadMethodCallException(
                (new Message('Unable to call %method% without a %type% instance'))
                    ->code('%method%', __METHOD__)
                    ->code('%type%', PhpScanner::class)
            );
        }
        $generator = new PoGenerator();
        $targetDir->createIfNotExists();
        $poFile = new File($targetDir->path()->getChild($this->domain . '.po'));
        $poFile->removeIfExists();
        /**
         * @var Translations $translations
         */
        foreach ($this->phpScanner->getTranslations() as $domain => $translations) {
            $translations->setLanguage($this->locale);
            $generator->generateFile($translations, $poFile->path()->toString());

            break;
        }
    }

    private function getIterator(): RecursiveIteratorIterator
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveFileFilterIterator(
                recursiveDirectoryIteratorFor($this->sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
                '.php'
            )
        );
        $iterator->rewind();

        return $iterator;
    }
}
