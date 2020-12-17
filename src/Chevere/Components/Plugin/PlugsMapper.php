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

namespace Chevere\Components\Plugin;

use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Plugin\PlugInterface;
use Chevere\Interfaces\Plugin\PlugsMapInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use function Safe\file_get_contents;

final class PlugsMapper
{
    private DirInterface $dir;

    private PlugsMapInterface $plugsMap;

    private RecursiveIteratorIterator $recursiveIterator;

    public function __construct(DirInterface $dir, PlugTypeInterface $plugType)
    {
        $dir->assertExists();
        $this->plugsMap = new PlugsMap($plugType);
        $this->dir = $dir;
        $dirIteratorFlags = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::KEY_AS_PATHNAME;
        $this->recursiveIterator = new RecursiveIteratorIterator(
            new PlugRecursiveFilterIterator(
                recursiveDirectoryIteratorFor($this->dir, $dirIteratorFlags),
                $plugType->trailingName()
            )
        );
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $this->classAnalyze($pathName);
            $this->recursiveIterator->next();
        }
    }

    public function plugsMap(): PlugsMapInterface
    {
        return $this->plugsMap;
    }

    private function classAnalyze(string $filename): void
    {
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
        }
    }
}
