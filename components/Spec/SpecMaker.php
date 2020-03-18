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

namespace Chevere\Components\Spec;

use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use LogicException;
use SplObjectStorage;

/**
 * Makes the application spec, which is a distributed json-fileset describing
 * the routing groups, routes and endpoints.
 */
final class SpecMaker
{
    private SpecPathInterface $specPath;

    private DirInterface $dir;

    private RouterInterface $router;

    private IndexSpec $indexSpec;

    public function __construct(
        SpecPathInterface $specPath,
        DirInterface $dir,
        RouterInterface $router
    ) {
        $this->specPath = $specPath;
        $this->dir = $dir;
        $this->assertDir();
        $this->router = $router;
        $this->assertRouter();
        $this->indexSpec = new IndexSpec($this->specPath);
        $routeableObjects = $router->routeables();
        $routeableObjects->rewind();
        $groups = [];
        while ($routeableObjects->valid()) {
            $id = $routeableObjects->getInfo();
            $routeable = $routeableObjects->current();
            $group = $router->groups()->getForId($id);
            $groupSpecPath = $specPath->getChild($group);
            if (!isset($group[$group])) {
                $groups[$group] = new GroupSpec($groupSpecPath);
            }
            /** @var GroupSpec $groupSpec */
            $groupSpec = $groups[$group]
                ->withAddedRouteable(
                    new RouteableSpec(
                        $groupSpecPath->getChild(
                            $router->named()->getForId($id)
                        ),
                        $routeable
                    )
                );
            $groups[$group] = $groupSpec;
            $routeableObjects->next();
        }
        foreach ($groups as $groupSpec) {
            $this->indexSpec = $this->indexSpec->withAddedGroup($groupSpec);
        }

        // xdd($this->indexSpec->toArray());
    }

    public function specIndex(): SpecIndexInterface
    {
        return $this->specIndex;
    }

    private function assertDir(): void
    {
        if (!$this->dir->exists()) {
            $this->dir->create(0777);
        }
        if (!$this->dir->path()->isWriteable()) {
            throw new LogicException(
                (new Message('Directory %pathName% is not writeable'))
                    ->code('%pathName%', $this->dir->path()->absolute())
                    ->toString()
            );
        }
    }

    private function assertRouter(): void
    {
        $checks = [
            'groups' => $this->router->hasGroups(),
            'index' => $this->router->hasIndex(),
            'named' => $this->router->hasNamed(),
            'regex' => $this->router->hasRegex(),
        ];
        $missing = array_filter($checks, fn (bool $bool) => $bool === false);
        $keys = array_keys($missing);
        if (!empty($keys)) {
            throw new SpecInvalidArgumentException(
                (new Message('Instance of %interfaceName% missing %propertyName% property(s).'))
                    ->code('%interfaceName%', RouterInterface::class)
                    ->code('%propertyName%', implode(', ', $keys))
                    ->toString()
            );
        }
        if ($this->router->routeables()->count() == 0) {
            throw new SpecInvalidArgumentException(
                (new Message('Instance of %interfaceName% does not contain any routeable.'))
                    ->code('%interfaceName%', RouterInterface::class)
                    ->toString()
            );
        }
    }
}
