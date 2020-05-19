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

namespace Chevere\Components\Console\Commands;

use Ahc\Cli\Input\Command;
use Chevere\Components\Controller\ControllerName;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Str\Str;
use Chevere\Components\Str\StrBool;
use Go\ParserReflection\ReflectionFile;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Throwable;

final class ControllerListCommand extends Command
{
    public function __construct()
    {
        parent::__construct('conlist', 'List controllers');
    }

    public function execute()
    {
        $dir = BootstrapInstance::get()->appDir();
        $this->directoryIterator = new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
        $this->recursiveIterator = new RecursiveIteratorIterator($this->recursiveFilterIterator());
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $parsedFile = new ReflectionFile($pathName);
            $fileNameSpaces = $parsedFile->getFileNamespaces();
            foreach ($fileNameSpaces as $namespace) {
                $classes = $namespace->getClasses();

                foreach ($classes as $class) {
                    try {
                        @(new ControllerName($class->getName()));
                        $this->writer()->colors('<green>•' . $class->getName() . '</end>', true);
                        $this->writer()->colors('<white>' . $pathName . '</end>', true);
                    } catch (Throwable $e) {
                    }
                }
            }
            $this->recursiveIterator->next();
        }
    }

    private function recursiveFilterIterator(): RecursiveFilterIterator
    {
        return new class($this->directoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }

                return (new StrBool($this->current()->getFilename()))
                    ->endsWith('.php');
            }
        };
    }
}
