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
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Str\Str;
use Chevere\Components\Str\StrBool;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\Filesystem\DirInterface;
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Throwable;
use UnexpectedValueException;
use function Chevere\Components\Filesystem\getDirFromString;

/**
 * @codeCoverageIgnore
 *
 * @property string $dir
 */
final class ControllerListCommand extends Command
{
    private RecursiveIteratorIterator $recursiveIterator;

    private DirInterface $cwd;

    private int $hit;

    public function __construct()
    {
        parent::__construct('conlist', 'Recursive list controllers in a given directory');
        $cwd = (new Str(getcwd()))->withRightTail('/')->toString();
        $this->cwd = getDirFromString($cwd);
        $this
            ->argument('<dir>', sprintf('A file system directory path'))
            ->usage(
                '<bold>  conlist</end> ## List controllers at cwd <comment>' . $this->cwd->path()->absolute() . ' </end> <eol/>' .
                '<bold>  conlist</end> <comment>dir/</end> ## List controllers under cwd child <comment>' . $this->cwd->path()->getChild('dir/')->absolute() . '</end><eol/>' .
                '<bold>  conlist</end> <comment>/var/</end> ## List controllers at <comment>/var/</end><eol/>'
            );
    }

    public function execute(): int
    {
        try {
            if ((new StrBool($this->dir))->startsWith('/')) {
                $dir = getDirFromString($this->dir);
            } else {
                $dir = $this->cwd->getChild($this->dir);
            }
            $dir->assertExists();
        } catch (Throwable $e) {
            $this->writer()
                ->error(get_class($e), true)
                ->raw(
                    $e instanceof Exception
                    ? $e->message()->toConsole()
                    : $e->getMessage()
                )
                ->eol();

            return 1;
        }
        $this->hit = 0;
        $this->writer()->okBold('List controllers @' . $dir->path()->absolute())->eol(2);
        $dirIterator = $this->getRecursiveDirectoryIterator($dir);
        $filterIterator = $this->getRecursiveFilterIterator($dirIterator);
        $this->recursiveIterator = new RecursiveIteratorIterator($filterIterator);
        try {
            $this->recursiveIterator->rewind();
        } catch (UnexpectedValueException $e) {
            $this->writer()
                ->error('Unable to rewind iterator: ')
                ->comment($e->getMessage())->eol(2)
                ->bold('🤔 Maybe try with user privileges?', true);

            return 255;
        }
        $this->iterate();
        $this->writer()->yellow(sprintf('Found %s controllers', (string) $this->hit))->eol();

        return 0;
    }

    private function iteratorNext(): void
    {
        try {
            $this->recursiveIterator->next();
        } catch (UnexpectedValueException $e) {
            $this->iteratorNext();
        }
    }

    private function getRecursiveDirectoryIterator(DirInterface $dir): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
    }

    private function getRecursiveFilterIterator(RecursiveDirectoryIterator $dirIterator): RecursiveFilterIterator
    {
        return new class($dirIterator) extends RecursiveFilterIterator
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

    private function iterate(): void
    {
        while ($this->recursiveIterator->valid()) {
            try {
                $parsedFile = new ReflectionFile(
                    $this->recursiveIterator->current()->getPathName()
                );
            } catch (Throwable $e) {
                $this->iteratorNext();
                continue;
            }
            /**
             * @var ReflectionFileNamespace $namespace
             */
            foreach ($parsedFile->getFileNamespaces() as $namespace) {
                $this->iterateClassesInNamespace($namespace);
            }
            $this->iteratorNext();
        }
    }

    private function iterateClassesInNamespace(ReflectionFileNamespace $namespace): void
    {
        $classes = $namespace->getClasses();
        /**
         * @var ReflectionClass $class
         */
        foreach ($classes as $class) {
            try {
                $controllerName = new ControllerName($class->getName());
                $this->hit++;
                $this->writer()
                    ->red('* ')
                    ->write($controllerName->toString(), true)
                    ->blue('  ' . $class->getFileName())
                    ->eol(2);
            } catch (Throwable $e) {
                continue;
            }
        }
    }
}
