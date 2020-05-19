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

use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Plugin\Interfaces\PlugInterface;
use Chevere\Components\Plugin\Interfaces\PlugsMapInterface;
use Chevere\Components\Plugin\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugin\PlugsMap;
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

final class PlugsMapper
{
    private DirInterface $dir;

    private PlugsMapInterface $plugsMap;

    private RecursiveIteratorIterator $recursiveIterator;

    /**
     * Iterates over the target $dir for plugs of type $plugType
     */
    public function __construct(DirInterface $dir, PlugTypeInterface $plugType)
    {
        $dir->assertExists();
        $this->plugsMap = new PlugsMap($plugType);
        $this->dir = $dir;
        $this->recursiveIterator = new RecursiveIteratorIterator(
            new PlugRecursiveFilterIterator(
                $this->getRecursiveDirectoryIterator(),
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

    private function getRecursiveDirectoryIterator(): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator(
            $this->dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
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
                        ->withAddedPlug(new AssertPlug($plug));
                }
            }
        }
    }
}
