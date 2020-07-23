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
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
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
            $parsedFile = new ReflectionFile($pathName);
            $this->namespaceClassesIterator($parsedFile->getFileNamespaces());
            $this->recursiveIterator->next();
        }
    }

    public function plugsMap(): PlugsMapInterface
    {
        return $this->plugsMap;
    }

    private function namespaceClassesIterator(array $reflectionFileNamespace): void
    {
        /**
         * @var ReflectionFileNamespace $namespace
         */
        foreach ($reflectionFileNamespace as $namespace) {
            /**
             * @var ReflectionClass $class
             */
            foreach ($namespace->getClasses() as $class) {
                if ($class->implementsInterface(PlugInterface::class)) {
                    $plug = $class->newInstance();
                    $this->plugsMap = $this->plugsMap
                        ->withAdded($plug);
                }
            }
        }
    }
}
