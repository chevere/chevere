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
use Chevere\Components\Plugs\Interfaces\PlugsMapInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\VarExportable\VarExportable;
use LogicException;

final class PlugsRegistry
{
    const REGISTRY_DIR = '%s-registry/';
    const PLUGS_FILENAME = '%s.php';
    const PLUGABLES_CLASSMAP_FILENAME = '%s_classmap.php';

    private DirInterface $dir;

    private DirInterface $registryDir;

    private string $plugsFileName;

    private string $plugablesClassMapFilename;

    private PlugsMapInterface $plugsMap;

    protected ClassMap $classMap;

    public function __construct(string $name, DirInterface $dir, PlugsMapInterface $plugsMap)
    {
        $this->dir = $dir;
        $this->assertDir();
        $this->registryDir = $this->dir->getChild(sprintf(self::REGISTRY_DIR, $name));
        $this->plugsFileName = sprintf(self::PLUGS_FILENAME, $name);
        $this->plugablesClassMapFilename = sprintf(self::PLUGABLES_CLASSMAP_FILENAME, $name);
        $this->plugsMap = $plugsMap;
        $this->classMap = new ClassMap;
        $this->putPlugs();
        $fileClassMap = new File($this->dir->path()
            ->getChild($this->plugablesClassMapFilename));
        if ($fileClassMap->exists() === false) {
            $fileClassMap->create();
        }
        $phpFileClassMap = new FilePhp($fileClassMap);
        (new FilePhpReturn($phpFileClassMap))
            ->put(new VarExportable($this->classMap));
        $phpFileClassMap->cache();

        return $this;
    }

    public function classMap(): ClassMap
    {
        return $this->classMap;
    }

    private function assertDir(): void
    {
        if ($this->dir->path()->isWritable() === false) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('Path %path% is not writeable'))
                    ->code('%path%', $this->dir->path()->absolute())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
    }

    private function putPlugs(): void
    {
        foreach ($this->plugsMap->map() as $plugableName => $queue) {
            $nsPath = (new Str($plugableName))->forwardSlashes()->toString();
            $plugsNsDir = $this->registryDir->getChild($nsPath . '/');
            $plugsPath = $plugsNsDir->path()
                ->getChild($this->plugsFileName);
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
            $this->classMap = $this->classMap->withPut(
                $plugableName,
                $plugsPath->absolute()
            );
        }
    }
}
