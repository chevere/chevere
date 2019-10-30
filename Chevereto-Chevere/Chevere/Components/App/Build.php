<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App;

use BadMethodCallException;
use Exception;

use Chevere\Components\Api\Api;
use Chevere\Components\Api\Maker as ApiMaker;
use Chevere\Components\App\Exceptions\AlreadyBuiltException;
use Chevere\Components\App\Exceptions\NoBuiltCacheException;
use Chevere\Components\App\Exceptions\NoBuiltFileException;
use Chevere\Components\App\Traits\ParametersAccessTrait;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker as RouterMaker;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\App\ServicesContract;
use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\Router\MakerContract;
use LogicException;

/**
 * The Build container.
 */
final class Build implements BuildContract
{
    use ParametersAccessTrait;

    /** @var ServicesContract */
    private $services;

    /** @var ParametersContract */
    private $parameters;

    /** @var Path */
    private $path;

    /** @var bool True if the App was built (cache) */
    private $isBuilt;

    /** @var CheckoutContract */
    private $checkout;

    /** @var array Containing the collection of Cache->toArray() data (checksums) */
    private $checksums;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var RouterMaker */
    private $routerMaker;

    /**
     * {@inheritdoc}
     */
    public function __construct(ServicesContract $services)
    {
        $this->isBuilt = false;
        $this->services = $services;
        $this->path = new Path('build/build.php');
    }

    /**
     * {@inheritdoc}
     */
    public function withServices(ServicesContract $services): BuildContract
    {
        $new = clone $this;
        $new->services = $services;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function services(): ServicesContract
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function withParameters(ParametersContract $parameters): BuildContract
    {
        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRouterMaker(MakerContract $maker): BuildContract
    {
        $new = clone $this;
        $new->routerMaker = $maker;

        return $new;
    }

    public function hasRouterMaker(): bool
    {
        return isset($this->routerMaker);
    }

    public function routerMaker(): MakerContract
    {
        return $this->routerMaker;
    }

    public function make(): BuildContract
    {
        $this->assertCanMake();
        $new = clone $this;
        $new->checksums = [];
        if ($new->parameters->hasApi()) {
            $new->makeApi();
        }
        if ($new->parameters->hasRoutes()) {
            $new->makeRouter();
        }
        $new->isBuilt = true;
        $new->checkout = new Checkout($new);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        if (!$this->path->isFile()) {
            throw new NoBuiltFileException();
        }
        $cachePath = new Path('cache');
        if (!$cachePath->isDir()) {
            throw new NoBuiltCacheException();
        }
        (new Dir($cachePath))
            ->removeContents();
        (new File($this->path))
            ->remove();
    }

    /**
     * {@inheritdoc}
     */
    public function isBuilt(): bool
    {
        return $this->isBuilt;
    }

    /**
     * {@inheritdoc}
     */
    public function path(): Path
    {
        return $this->path;
    }
    
    /**
     * {@inheritdoc}
     */
    public function checksums(): array
    {
        return $this->checksums;
    }

    /**
     * {@inheritdoc}
     */
    public function checkout(): CheckoutContract
    {
        return $this->checkout;
    }

    private function assertCanMake(): void
    {
        foreach ([
            'parameters' => ParametersContract::class,
            'routerMaker' => MakerContract::class
        ] as $property => $contract) {
            if (!isset($this->{$property})) {
                $missing[] = (new Message('%s'))->code('%s', $contract)->toString(0);
            }
        }
        if (isset($missing)) {
            throw new LogicException(
                (new Message('Method %method% can be only called when the instance of %className% has %contracts%'))
                    ->code('%method%', __METHOD__)
                    ->code('%className%', __CLASS__)
                    ->strtr('%contracts%', implode(', ', $missing))
                    ->toString()
            );
        }
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
    }

    private function makeApi(): void
    {
        $this->apiMaker = new ApiMaker($this->routerMaker);
        $this->apiMaker = $this->apiMaker
            ->withPath(
                new Path(
                    $this->parameters->api()
                )
            );
        $this->services = $this->services
            ->withApi(
                (new Api())
                    ->withMaker($this->apiMaker)
            );
        $this->apiMaker = $this->apiMaker
            ->withCache(
                new Cache('api', new Path('build'))
            );
        $this->checksums = $this->apiMaker->cache()->toArray();
    }

    private function makeRouter(): void
    {
        $this->routerMaker = $this->routerMaker
            ->withAddedRouteFiles(...$this->parameters->routes());
        $this->services = $this->services
            ->withRouter(
                (new Router())
                    ->withMaker($this->routerMaker)
            );
        $this->routerMaker = $this->routerMaker
            ->withCache(
                new Cache('router', new Path('build'))
            );
        $this->checksums = array_merge($this->routerMaker->cache()->toArray(), $this->checksums);
    }
}
