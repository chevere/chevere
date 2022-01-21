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
use Chevere\Components\Writer\NullWriter;
use Chevere\Components\Writer\traits\WriterTrait;
use Chevere\Exceptions\Core\BadMethodCallException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Translator\PoMakerInterface;
use Gettext\Generator\PoGenerator;
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * @method self withWriter(WriterInterface $writer)
 */
final class PoMaker implements PoMakerInterface
{
    use WriterTrait;

    public const FUNCTIONS = [
        '__' => 'gettext',
        '__f' => 'gettext',
        '__t' => 'gettext',
        '__n' => 'ngettext',
        '__nf' => 'ngettext',
        '__nt' => 'ngettext',
    ];

    private DirInterface $sourceDir;

    private PhpScanner $phpScanner;

    public function __construct(
        private string $locale,
        private string $domain
    ) {
        $this->writer = new NullWriter();
    }

    public function withScanFor(DirInterface $sourceDir): self
    {
        $new = clone $this;
        $sourceDir->assertExists();
        $new->sourceDir = $sourceDir;
        $new->phpScanner = new PhpScanner(Translations::create($new->domain));
        $new->phpScanner->setDefaultDomain($new->domain);
        $new->phpScanner->setFunctions(self::FUNCTIONS);
        $iterator = $new->getIterator();
        $this->writer->write(
            sprintf("📂 Starting dir %s iteration\n", $new->sourceDir->path()->__toString())
        );
        $iterator->rewind();
        while ($iterator->valid()) {
            $pathName = $iterator->current()->getPathName();
            $new->writer->write("- File ${pathName}\n");

            try {
                $new->phpScanner->scanFile($pathName);
            }
            // @codeCoverageIgnoreStart
            catch (Throwable $e) {
                throw new LogicException(
                    previous: $e,
                    message: new Message('Unable to scan file.')
                );
            }
            // @codeCoverageIgnoreEnd
            $iterator->next();
        }
        $this->writer->write("💯 Done!\n");

        return $new;
    }

    public function make(DirInterface $targetDir): void
    {
        if (!isset($this->phpScanner)) {
            throw new BadMethodCallException(
                (new Message('Unable to call %method% without a %type% instance'))
                    ->code('%method%', __METHOD__)
                    ->code('%type%', PhpScanner::class)
            );
        }
        $generator = new PoGenerator();
        $targetDir = $targetDir->getChild($this->locale . '/');
        $targetDir->createIfNotExists();
        $poFile = new File($targetDir->path()->getChild($this->domain . '.po'));
        $poFile->removeIfExists();
        /**
         * @var Translations $translations
         */
        foreach ($this->phpScanner->getTranslations() as $translations) {
            $translations->setLanguage($this->locale);

            try {
                $generator->generateFile($translations, $poFile->path()->__toString());
            }
            // @codeCoverageIgnoreStart
            catch (\InvalidArgumentException $e) {
                throw new LogicException(
                    previous: $e,
                    message: new Message('Unable to make translation.')
                );
            }
            // @codeCoverageIgnoreEnd

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
