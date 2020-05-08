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
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\PlugsRegister;
use Chevere\Components\Str\StrBool;
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

final class PlugsIterator
{
    const HOOK_TRAILING_NAME = 'Hook.php';

    private DirInterface $dir;

    private PlugsRegister $plugsRegister;

    private RecursiveIteratorIterator $recursiveIterator;

    /**
     * Iterates over the target dir for files matching *Hook.php and implementing
     * HookInterface
     */
    public function __construct(DirInterface $dir)
    {
        if ($dir->exists() === false) {
            throw new LogicException(
                (new Message('No dir existst at %path%'))
                    ->code('%path%', $dir->path()->absolute())
                    ->toString()
            );
        }
        $this->dir = $dir;
        $this->plugsRegister = new PlugsRegister;
        $this->directoryIterator = $this->getDirectoryIterator();
        $this->recursiveIterator = new RecursiveIteratorIterator(
            $this->recursiveFilterIterator()
        );
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $parsedFile = new ReflectionFile($pathName);
            $this->namespaceClassesIterator($parsedFile->getFileNamespaces());
            $this->recursiveIterator->next();
        }
    }

    public function plugsRegister(): PlugsRegister
    {
        return $this->plugsRegister;
    }

    private function getDirectoryIterator(): RecursiveDirectoryIterator
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
            $classes = $namespace->getClasses();
            /**
             * @var ReflectionClass $class
             */
            foreach ($classes as $class) {
                if ($class->implementsInterface(HookInterface::class)) {
                    $this->plugsRegister = $this->plugsRegister
                        ->withAddedPlug($class->newInstance());
                }
            }
        }
    }

    private function recursiveFilterIterator(): RecursiveFilterIterator
    {
        return new class($this->directoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true; // @codeCoverageIgnore
                }

                return (new StrBool($this->current()->getFilename()))->endsWith(PlugsIterator::HOOK_TRAILING_NAME);
            }
        };
    }
}
