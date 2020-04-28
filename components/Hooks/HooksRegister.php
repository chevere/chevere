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
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\Str;
use Chevere\Components\Variable\VariableExport;
use Ds\Map;
use Ds\Set;
use LogicException;

final class HooksRegister
{
    const HOOKS_DIR = 'hooks/';
    const HOOKS_FILENAME = 'hooks.php';
    const HOOKABLES_CLASSMAP_FILENAME = 'hookables_classmap.php';

    private Set $set;

    private Map $queues;

    private HookablesMap $map;

    public function __construct()
    {
        $this->set = new Set;
        $this->queues = new Map;
        $this->map = new HookablesMap;
    }

    public function withAddedHook(HookInterface $hook): HooksRegister
    {
        $hookClassName = get_class($hook);
        if ($this->set->contains($hookClassName)) {
            throw new LogicException(
                (new Message('Hook %hook% has been already registered'))
                    ->code('%hook%', $hookClassName)
                    ->toString()
            );
        }
        $new = clone $this;
        $new->queues->put($hook->className(), (new HooksQueue)->withHook($hook));

        return $new;
    }

    public function withHookablesClassMap(DirInterface $dir): HooksRegister
    {
        if ($dir->path()->isWritable() === false) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('Path %path% is not writeable'))
                    ->code('%path%', $dir->path()->absolute())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
        $hooksDir = $dir->getChild(self::HOOKS_DIR);
        $new = clone $this;
        foreach ($new->queues as $hookableClassName => $queue) {
            $nsPath = (string) (new Str($hookableClassName))->forwardSlashes();
            $hooksNsDir = $hooksDir->getChild($nsPath . '/');
            $hooksPath = $hooksNsDir->path()->getChild(self::HOOKS_FILENAME);
            if ($hooksPath->exists() && $hooksPath->isWritable() === false) {
                // @codeCoverageIgnoreStart
                throw new LogicException(
                    (new Message('Path %path% is not writable'))
                        ->code('%path%', $hooksPath->absolute())
                        ->toString()
                );
                // @codeCoverageIgnoreEnd
            }
            $fileHooks = new File($hooksPath);
            if ($fileHooks->exists() === false) {
                $fileHooks->create();
            }
            $phpFileHooks = new PhpFile($fileHooks);
            (new PhpFileReturn($phpFileHooks))->put(
                new VariableExport($queue)
            );
            $phpFileHooks->cache();
            $new->map = $new->map->withPut($hookableClassName, $hooksPath->absolute());
        }
        $fileClassMap = new File($dir->path()
            ->getChild(self::HOOKABLES_CLASSMAP_FILENAME));
        if ($fileClassMap->exists() === false) {
            $fileClassMap->create();
        }
        $phpFileClassMap = new PhpFile($fileClassMap);
        (new PhpFileReturn($phpFileClassMap))
            ->put(new VariableExport($new->map));
        $phpFileClassMap->cache();

        return $new;
    }

    public function hookablesMap(): HookablesMap
    {
        return $this->map;
    }
}
