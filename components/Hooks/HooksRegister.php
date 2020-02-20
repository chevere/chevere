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

namespace Chevere\Components\Hooks;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\Str;
use Chevere\Components\Variable\VariableExport;
use LogicException;
use ReflectionClass;

final class HooksRegister
{
    const HOOKS_FOLDER = 'hooks';
    const HOOKS_FILENAME = 'hooks.php';
    const HOOKABLES_CLASSMAP_FILENAME = 'hookables_classmap.php';

    /** @var array hookableClassName => [anchor => [priority => hookClassName]], */
    private array $map = [];

    private array $hookablesClassMap = [];

    // private array $hooksClassMap = [];

    public function withAddedHook(AssertHook $assertHook): HooksRegister
    {
        $hook = $assertHook->hook();
        $reflection = new ReflectionClass($hook);
        $new = clone $this;
        $new->map[$hook::hookableClassName()][$hook::anchor()][$hook::priority()][] = $reflection->getName();
        // $new->hooksClassMap[$reflection->getName()] = $reflection->getFileName();

        return $new;
    }

    public function withHookablesClassMap(DirInterface $dir): HooksRegister
    {
        if ($dir->path()->isWriteable() === false) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('Path %path% is not writeable'))
                    ->code('%path%', $dir->path()->absolute())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
        $hooksDir = $dir->getChild(self::HOOKS_FOLDER);
        $new = clone $this;
        foreach ($new->map as $className => $hooks) {
            $nsPath = (string) (new Str($className))->forwardSlashes();
            $hooksNsDir = $hooksDir->getChild($nsPath);
            $filePath = $hooksNsDir->path()->getChild(self::HOOKS_FILENAME);
            if ($filePath->exists() && $filePath->isWriteable() === false) {
                // @codeCoverageIgnoreStart
                throw new LogicException(
                    (new Message('Path %path% is not writeable'))
                        ->code('%path%', $filePath->absolute())
                        ->toString()
                );
                // @codeCoverageIgnoreEnd
            }
            $file = new File($filePath);
            if ($file->exists() === false) {
                $file->create();
            }
            $phpFile = new PhpFile($file);
            (new PhpFileReturn($phpFile))->put(new VariableExport($hooks));
            $phpFile->cache();
            $new->hookablesClassMap[$className] = $filePath->absolute();
        }
        $file = new File($dir->path()->getChild(self::HOOKABLES_CLASSMAP_FILENAME));
        if ($file->exists() === false) {
            $file->create();
        }
        $phpFile = new PhpFile($file);
        (new PhpFileReturn($phpFile))->put(new VariableExport($new->hookablesClassMap));
        $phpFile->cache();

        return $new;
    }

    public function hookablesClassMap(): array
    {
        return $this->hookablesClassMap;
    }
}
