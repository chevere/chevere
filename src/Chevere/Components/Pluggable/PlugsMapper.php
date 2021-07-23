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

namespace Chevere\Components\Pluggable;

use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Components\Iterator\RecursiveFileFilterIterator;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Writer\NullWriter;
use Chevere\Components\Writer\traits\WriterTrait;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Pluggable\PlugInterface;
use Chevere\Interfaces\Pluggable\PlugsMapInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use function Safe\file_get_contents;

/**
 * @method self withWriter(WriterInterface $writer)
 */
final class PlugsMapper
{
    use WriterTrait;

    private PlugsMapInterface $plugsMap;

    public function __construct(
        private PlugTypeInterface $plugType
    ) {
        $this->writer = new NullWriter();
        $this->plugsMap = new PlugsMap($plugType);
    }

    public function withPlugsMapFor(DirInterface $dir): self
    {
        $dir->assertExists();
        $new = clone $this;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveFileFilterIterator(
                recursiveDirectoryIteratorFor(
                    $dir,
                    RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::KEY_AS_PATHNAME
                ),
                $new->plugType->trailingName()
            )
        );
        $this->writer->write(
            sprintf("📂 Starting dir %s iteration\n", $dir->path()->toString())
        );
        $iterator->rewind();
        while ($iterator->valid()) {
            $pathName = $iterator->current()->getPathName();
            $new->classAnalyze($pathName);
            $iterator->next();
        }

        return $new;
    }

    public function plugsMap(): PlugsMapInterface
    {
        return $this->plugsMap;
    }

    private function classAnalyze(string $filename): void
    {
        $this->writer->write("- File {$filename}\n");
        $flag = '(skip)';
        $regex = new Regex('/namespace (.*);[\S\s]* class (\S*) .*/');
        $matches = $regex->match(file_get_contents($filename));
        $namespace = $matches[1];
        $className = $matches[2];
        /** @var class-string */
        $classString = "${namespace}\\${className}";
        $reflection = new ReflectionClass($classString);
        if (! $reflection->isInterface() && $reflection->implementsInterface(PlugInterface::class)) {
            $plugName = $reflection->getName();
            /** @var PlugInterface $plug */
            $plug = new $plugName();
            $this->plugsMap = $this->plugsMap
                ->withAdded($plug);
            $flag = "> ${plugName}";
        }
        $this->writer->write(" ${flag}\n");
    }
}
