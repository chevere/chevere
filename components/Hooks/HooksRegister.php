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
use Chevere\Components\Hooks\Interfaces\HookInterface;
use ReflectionClass;

final class HooksRegister
{
    private array $map = [];

    // private array $hooksClassMap = [];

    public function withAddedHook(HookInterface $hook): HooksRegister
    {
        $new = clone $this;
        $reflection = new ReflectionClass($hook);
        $new->map[$hook::forClassName()][$hook::anchor()][$hook::priority()][] = $reflection->getName();
        // $new->hooksClassMap[$reflection->getName()] = $reflection->getFileName();

        return $new;
    }

    public function withClassMap(DirInterface $dir): void
    {
        // hookable_classmap.php
        // hooks_classmap.php
        // ./hooks/**NAMESPACE**/ClassName.php
        $new = clone $this;
        $path = $dir->path();
        foreach ($new->map as $className => $hooks) {
            $fileName = $path->getChild($className . '/hooks.php') ;
        }
        $file = new File($path->getChild('hookables_classmap.php'));
        $phpFile = new PhpFile($file);
    }

    public function hasClassMap(): bool
    {
        return isset($this->classMap);
    }

    public function hooksClassMap(): array
    {
        return $this->hooksClassMap;
    }

    // public function hookablesClassMap(): array
    // {
    //     return $this->hookablesClassMap;
    // }
}
// return [
//     'Chevere\Components\Hooks\Tests\MyHookable' => '/home/rodolfo/git/chevere/components/Hooks/Tests/_resources/hooks/Chevere/Components/Hooks/Tests/MyHookable/hooks.php',
//     'Chevere\Components\Hooks\Tests\MyHookableWithCorruptedHooks' => '/home/rodolfo/git/chevere/components/Hooks/Tests/_resources/hooks/Chevere/Components/Hooks/Tests/MyHookableWithCorruptedHooks/hooks.php',
//     'Chevere\Components\Hooks\Tests\MyHookableWithMissingHooks' => 'error.php'
// ];

// return [
//     'setString' => [
//         0 => [
//             'Chevere\Components\Hooks\Tests\MyHook'
//         ],
//     ],
// ];
