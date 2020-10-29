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

use Chevere\Components\Plugin\PlugsMap;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Plugin\PlugInterface;
use Chevere\Interfaces\Plugin\PlugsMapInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;

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
            $astLocator = (new BetterReflection)->astLocator();
            $reflector = new ClassReflector(
                new AggregateSourceLocator([
                    new AutoloadSourceLocator($astLocator),
                    new SingleFileSourceLocator($pathName, $astLocator),
                ])
            );
            $classes = $reflector->getAllClasses();
            $this->classesIterator($classes);
            $this->recursiveIterator->next();
        }
    }

    public function plugsMap(): PlugsMapInterface
    {
        return $this->plugsMap;
    }

    /**
     * @param ReflectionClass[]
     */
    private function classesIterator(array $classes): void
    {
        /**
         * @var ReflectionClass $class
         */
        foreach ($classes as $class) {
            if (!$class->isInterface() && $class->implementsInterface(PlugInterface::class)) {
                $plugName = $class->getName();
                /**
                 * @var PlugInterface $plug
                 */
                $plug = new $plugName;
                $this->plugsMap = $this->plugsMap
                    ->withAdded($plug);
            }
        }
    }
}
