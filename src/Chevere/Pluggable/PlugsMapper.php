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

namespace Chevere\Pluggable;

use Chevere\Filesystem\Interfaces\DirInterface;
use function Chevere\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Iterator\RecursiveFileFilterIterator;
use Chevere\Pluggable\Interfaces\PlugInterface;
use Chevere\Pluggable\Interfaces\PlugsMapInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Regex\Regex;
use Chevere\Writer\NullWriter;
use Chevere\Writer\Traits\WriterTrait;
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
            sprintf("📂 Starting dir %s iteration\n", $dir->path()->__toString())
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
        if (!$reflection->isInterface() && $reflection->implementsInterface(PlugInterface::class)) {
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
