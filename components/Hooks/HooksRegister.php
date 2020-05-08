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

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Extend\PluginsQueue;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\Str;
use Chevere\Components\VarExportable\VarExportable;
use Ds\Map;
use Ds\Set;
use LogicException;

final class HooksRegister
{
    const HOOKS_DIR = 'hooks-reg/';
    const HOOKS_FILENAME = 'hooks.php';
    const HOOKABLES_CLASSMAP_FILENAME = 'hookables_classmap.php';

    private Set $set;

    private Map $map;

    private ClassMap $hookablesToHooks;

    public function __construct()
    {
        $this->set = new Set;
        $this->map = new Map;
        $this->hookablesToHooks = new ClassMap;
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
        $pluginQueue = $this->map->hasKey($hook->at())
            ? $this->map->get($hook->at())
            : new PluginsQueue;
        $new = clone $this;
        $new->map->put($hook->at(), $pluginQueue->withPlugin($hook));

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
        foreach ($new->map as $hookableClassName => $queue) {
            $nsPath = (new Str($hookableClassName))->forwardSlashes()
                ->toString();
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
            $phpFileHooks = new FilePhp($fileHooks);
            (new FilePhpReturn($phpFileHooks))
                ->put(new VarExportable($queue));
            $phpFileHooks->cache();
            $new->hookablesToHooks = $new->hookablesToHooks->withPut(
                $hookableClassName,
                $hooksPath->absolute()
            );
        }
        $fileClassMap = new File($dir->path()
            ->getChild(self::HOOKABLES_CLASSMAP_FILENAME));
        if ($fileClassMap->exists() === false) {
            $fileClassMap->create();
        }
        $phpFileClassMap = new FilePhp($fileClassMap);
        (new FilePhpReturn($phpFileClassMap))
            ->put(new VarExportable($new->hookablesToHooks));
        $phpFileClassMap->cache();

        return $new;
    }

    public function hooksQueueMap(): Map
    {
        return $this->map;
    }

    public function classMap(): ClassMap
    {
        return $this->hookablesToHooks;
    }
}
