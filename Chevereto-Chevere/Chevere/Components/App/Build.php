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

use Exception;

use Chevere\Components\Api\Api;
use Chevere\Components\Api\Maker as ApiMaker;
use Chevere\Components\App\Exceptions\AlreadyBuiltException;
use Chevere\Components\App\Traits\ParametersAccessTrait;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker as RouterMaker;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\App\ContainerContract;
use Chevere\Contracts\App\ParametersContract;

/**
 * The Build container.
 *
 * Allows to interact with the application build, which refers to the base application service layer
 * which consists in API and Router services.
 */
final class Build implements BuildContract
{
    use ParametersAccessTrait;

    /** @var BuilderContract */
    private $builder;

    /** @var ContainerContract */
    private $container;

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
    public function __construct()
    {
        $this->isBuilt = false;
        $this->container = new Container();
        $this->path = new Path(BuildContract::FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function withContainer(ContainerContract $container): BuildContract
    {
        $new = clone $this;
        $new->container = $container;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function container(): ContainerContract
    {
        return $this->container;
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
    public function make(): BuildContract
    {
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $new = clone $this;
        $new->routerMaker = new RouterMaker();
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
        (new File($this->path))
            ->remove();
        (new Dir(new Path('cache')))
            ->removeContents();
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
        $this->assertHasChecksums();

        return $this->checksums;
    }

    /**
     * {@inheritdoc}
     */
    public function checkout(): CheckoutContract
    {
        $this->assertHasCheckout();

        return $this->checkout;
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
        $this->container = $this->container
            ->withApi(
                (new Api())
                    ->withMaker($this->apiMaker)
            );
        $this->apiMaker = $this->apiMaker
            ->withCache();
        $this->checksums = $this->apiMaker->cache()->toArray();
    }

    private function makeRouter(): void
    {
        $this->routerMaker = $this->routerMaker
            ->withAddedRouteFiles(...$this->parameters->routes());
        $this->container = $this->container
            ->withRouter(
                (new Router())
                    ->withMaker($this->routerMaker)
            );
        $this->routerMaker = $this->routerMaker
            ->withCache();
        $this->checksums = array_merge($this->routerMaker->cache()->toArray(), $this->checksums);
    }

    private function assertHasChecksums(): void
    {
        if (!isset($this->checksums)) {
            throw new Exception(
                (new Message("Property %type% %property% is not set for %className% instance"))
                    ->code('%type%', CheckoutContract::class)
                    ->code('%property%', '$checksums')
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
    }

    private function assertHasCheckout(): void
    {
        if (!isset($this->checkout)) {
            throw new Exception(
                (new Message("Property %type% %property% is not set for %className% instance"))
                    ->code('%type%', CheckoutContract::class)
                    ->code('%property%', '$checkout')
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
    }
}
