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

namespace Chevere\Components\Plugs;

use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\PlugsMapper;
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

final class PlugsIterator
{
    private DirInterface $dir;

    private PlugsMapper $plugsMapper;

    private RecursiveIteratorIterator $recursiveIterator;

    /**
     * Iterates over the target dir for files matching *Hook.php and implementing
     * HookInterface
     */
    public function __construct(DirInterface $dir, PlugTypeInterface $plugType)
    {
        if ($dir->exists() === false) {
            throw new LogicException(
                (new Message('No dir existst at %path%'))
                    ->code('%path%', $dir->path()->absolute())
                    ->toString()
            );
        }
        $this->dir = $dir;
        $this->plugsMapper = new PlugsMapper;
        $this->recursiveIterator = new RecursiveIteratorIterator(
            new PlugsRecursiveFilterIterator(
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

    public function plugsMapper(): PlugsMapper
    {
        return $this->plugsMapper;
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
                    $this->plugsMapper = $this->plugsMapper
                        ->withAddedPlug(new AssertPlug($plug));
                }
            }
        }
    }
}
