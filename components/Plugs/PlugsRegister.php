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

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\PlugsQueue;
use Chevere\Components\Str\Str;
use Chevere\Components\VarExportable\VarExportable;
use Ds\Map;
use Ds\Set;
use LogicException;

class PlugsRegister
{
    const REGISTRY_DIR = 'hooks-reg/'; // %s-register/
    const PLUGS_FILENAME = 'hooks.php'; // %s.php
    const CLASSMAP_FILENAME = 'hookables_classmap.php'; // %s_classmap.php

    private Set $set;

    private Map $map;

    private ClassMap $classMap;

    public function __construct()
    {
        $this->set = new Set;
        $this->map = new Map;
        $this->classMap = new ClassMap;
    }

    public function withAddedPlug(PlugInterface $plug): PlugsRegister
    {
        $plugName = get_class($plug);
        if ($this->set->contains($plugName)) {
            throw new LogicException(
                (new Message('%plugName% has been already registered'))
                    ->code('%plugName%', $plugName)
                    ->toString()
            );
        }
        $plugsQueue = $this->map->hasKey($plug->at())
            ? $this->map->get($plug->at())
            : new PlugsQueue;
        $new = clone $this;
        $new->map->put($plug->at(), $plugsQueue->withPlug($plug));

        return $new;
    }

    public function withClassMapAt(DirInterface $dir): PlugsRegister
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
        $plugsDir = $dir->getChild(self::REGISTRY_DIR);
        $new = clone $this;
        foreach ($new->map as $plugableClassName => $queue) {
            $nsPath = (new Str($plugableClassName))->forwardSlashes()
                ->toString();
            $plugsNsDir = $plugsDir->getChild($nsPath . '/');
            $plugsPath = $plugsNsDir->path()->getChild(self::PLUGS_FILENAME);
            if ($plugsPath->exists() && $plugsPath->isWritable() === false) {
                // @codeCoverageIgnoreStart
                throw new LogicException(
                    (new Message('Path %path% is not writable'))
                        ->code('%path%', $plugsPath->absolute())
                        ->toString()
                );
                // @codeCoverageIgnoreEnd
            }
            $filePlugs = new File($plugsPath);
            if ($filePlugs->exists() === false) {
                $filePlugs->create();
            }
            $phpFilePlugs = new FilePhp($filePlugs);
            (new FilePhpReturn($phpFilePlugs))
                ->put(new VarExportable($queue));
            $phpFilePlugs->cache();
            $new->classMap = $new->classMap->withPut(
                $plugableClassName,
                $plugsPath->absolute()
            );
        }
        $fileClassMap = new File($dir->path()
            ->getChild(self::CLASSMAP_FILENAME));
        if ($fileClassMap->exists() === false) {
            $fileClassMap->create();
        }
        $phpFileClassMap = new FilePhp($fileClassMap);
        (new FilePhpReturn($phpFileClassMap))
            ->put(new VarExportable($new->classMap));
        $phpFileClassMap->cache();

        return $new;
    }

    public function map(): Map
    {
        return $this->map;
    }

    public function classMap(): ClassMap
    {
        return $this->classMap;
    }
}
