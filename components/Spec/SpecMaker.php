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

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\RouteEndpointSpec;
use Chevere\Components\Str\Str;
use Ds\Map;
use LogicException;

/**
 * Makes the application spec, which is a distributed json-fileset describing
 * the routing groups, routes and endpoints.
 */
final class SpecMaker
{
    private SpecPathInterface $specPath;

    private DirInterface $dir;

    private RouterInterface $router;

    private SpecIndex $specIndex;

    private IndexSpec $indexSpec;

    private Map $files;

    public function __construct(
        SpecPathInterface $specRoot,
        DirInterface $dir,
        RouterInterface $router
    ) {
        $this->specPath = $specRoot;
        $this->dir = $dir;
        $this->assertDir();
        $this->router = $router;
        $this->assertRouter();
        $this->specIndex = new SpecIndex;
        $this->indexSpec = new IndexSpec($this->specPath);
        $this->files = new Map;
        $routes = $router->routeables();
        $groups = [];
        /**
         * @var string $routeName
         * @var Routeable $routeabe
         */
        foreach ($routes->map() as $routeName => $routeable) {
            $groupName = $router->groups()->getForRouteName($routeName);
            if (!isset($groupName[$groupName])) {
                $groups[$groupName] = new GroupSpec($specRoot, $groupName);
            }
            $routeableSpec = new RouteableSpec(
                $specRoot->getChild($groupName),
                $routeable
            );
            $this->makeJsonFile($routeableSpec);
            /** @var GroupSpec $groupSpec */
            $groupSpec = $groups[$groupName];
            $groupSpec = $groupSpec->withAddedRouteableSpec($routeableSpec);
            $groups[$groupName] = $groupSpec;
            $routeEndpointSpecs = $routeableSpec->routeEndpointSpecs();
            /** @var RouteEndpointSpec $routeEndpointSpec */
            foreach ($routeEndpointSpecs->map() as $routeEndpointSpec) {
                $this->specIndex = $this->specIndex->withOffset(
                    $routeName,
                    $routeEndpointSpec
                );
                $this->makeJsonFile($routeEndpointSpec);
            }
        }
        foreach ($groups as $groupSpec) {
            $this->makeJsonFile($groupSpec);
            $this->indexSpec = $this->indexSpec->withAddedGroup($groupSpec);
        }
        $this->makeJsonFile($this->indexSpec);
    }

    public function specIndex(): SpecIndexInterface
    {
        return $this->specIndex;
    }

    public function files(): Map
    {
        return $this->files->copy();
    }

    /**
     * @codeCoverageIgnore
     * @throws LogicException
     */
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
        $this->dir->removeContents();
    }

    private function assertRouter(): void
    {
        $checks = [
            'groups' => $this->router->hasGroups(),
            'index' => $this->router->hasIndex(),
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
        // @codeCoverageIgnoreStart
        if ($this->router->routeables()->map()->count() == 0) {
            throw new LogicException(
                (new Message('Instance of %interfaceName% does not contain any routeable.'))
                    ->code('%interfaceName%', RouterInterface::class)
                    ->toString()
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function makeJsonFile(SpecInterface $spec): void
    {
        $filePath = $this->getPathFor($spec->jsonPath());
        $this->files[$spec->jsonPath()] = $filePath;
        $file = new File($filePath);
        if ($file->exists()) {
            $file->remove(); // @codeCoverageIgnore
        }
        $file->create();
        $file->put($this->toJson($spec->toArray()));
    }

    private function getPathFor(string $jsonPath): PathInterface
    {
        $dirPath = $this->dir->path(); // /home/weas/spec/
        $child = (string) (new Str($jsonPath))
            ->replaceFirst($this->specPath->pub(), '');
        $child = ltrim($child, '/');

        return $dirPath->getChild($child);
    }

    private function toJson(array $array): string
    {
        return json_encode($array, JSON_PRETTY_PRINT);
    }
}
