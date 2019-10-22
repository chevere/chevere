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

use LogicException;

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
use Chevere\Contracts\App\ParametersContract;

/**
 * Allows to create the application cache.
 */
final class Build implements BuildContract
{
    use ParametersAccessTrait;

    /** @var BuilderContract */
    private $builder;

    /** @var Container */
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

    public function __construct()
    {
        $this->isBuilt = false;
        $this->container = new Container();
        $this->path = new Path(BuildContract::FILE_PATH);
    }

    public function withContainer(Container $container): BuildContract
    {
        $new = clone $this;
        $new->container = $container;

        return $new;
    }

    public function withParameters(ParametersContract $parameters): BuildContract
    {
        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }

    public function make(): BuildContract
    {
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $new = clone $this;
        $new->routerMaker = new RouterMaker();
        $new->checksums = [];
        if ($new->parameters->hasApi()) {
            $new->handleApi();
        }
        if ($new->parameters->hasRoutes()) {
            $new->handleRoutes();
        }
        $new->checkout = new Checkout($new);
        $new->isBuilt = true;

        return $new;
    }

    public function isBuilt(): bool
    {
        return $this->isBuilt;
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function container(): Container
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function checksums(): array
    {
        return $this->checksums;
    }

    public function checkout(): CheckoutContract
    {
        $this->assertHasCheckout();

        return $this->checkout;
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

    private function handleApi(): void
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

    private function handleRoutes(): void
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

    private function assertHasCheckout(): void
    {
        if (!isset($this->checkout)) {
            throw new LogicException(
                (new Message("Property %type% %property% is not set for object of %className% class"))
                    ->code('%type%', CheckoutContract::class)
                    ->code('%property%', 'checkout')
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
    }
}
