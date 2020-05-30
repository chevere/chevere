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
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Str\StrBool;
use Chevere\Exceptions\Core\Exception;
use Go\ParserReflection\ReflectionFile;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * @codeCoverageIgnore
 */
final class ControllerListCommand extends Command
{
    private RecursiveDirectoryIterator $directoryIterator;

    public function __construct()
    {
        parent::__construct('conlist', 'List controllers in a given path');
        $this
            ->argument('<path>', sprintf('A trailing slash absolute directory path'))
            ->usage(
                '<bold>  conlist</end> <comment>/var/</end> ## List controllers at <comment>/var/</end><eol/>'
            );
    }

    public function execute(): int
    {
        try {
            $dir = new DirFromString($this->path);
            $dir->assertExists();
        } catch (Throwable $e) {
            $this->writer()
                ->error(get_class($e))
                ->eol()
                ->raw(
                    $e instanceof Exception
                    ? $e->message()->toConsole()
                    : $e->getMessage()
                )
                ->eol();

            return 1;
        }

        $this->writer()->ok('List controllers @' . $dir->path()->absolute())->eol()->eol();
        $this->directoryIterator = new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
        $this->recursiveIterator = new RecursiveIteratorIterator($this->recursiveFilterIterator());
        $this->recursiveIterator->rewind();
        $hit = 0;
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            try {
                $parsedFile = new ReflectionFile($pathName);
            } catch (Throwable $e) {
                $this->recursiveIterator->next();
                continue;
            }
            $fileNameSpaces = $parsedFile->getFileNamespaces();
            foreach ($fileNameSpaces as $namespace) {
                $classes = $namespace->getClasses();
                foreach ($classes as $class) {
                    try {
                        new ControllerName($class->getName());
                        $hit++;
                        $this->writer()
                            ->red('* ')
                            ->write($class->getName(), true)
                            ->blue('  ' . $pathName, true)
                            ->eol();
                    } catch (Throwable $e) {
                        continue;
                    }
                }
            }
            $this->recursiveIterator->next();
        }
        $this->writer()->yellow(sprintf('Found %s controllers', (string) $hit))->eol();

        return 0;
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
                    ->endsWith('Controller.php');
            }
        };
    }
}
