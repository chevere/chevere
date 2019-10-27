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

use Chevere\Components\App\Instances\RequestInstance;
use Chevere\Components\Controller\Traits\ControllerNameAccessTrait;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Http\RequestContract;

/**
 * The application builder container.
 */
final class Builder implements BuilderContract
{
    use ControllerNameAccessTrait;

    /** @var AppContract */
    private $app;

    /** @var BuildContract */
    private $build;

    /** @var RequestContract */
    private $request;

    /** @var array */
    private $controllerArguments;

    /**
     * {@inheritdoc}
     */
    public function __construct(AppContract $app, BuildContract $build)
    {
        $this->app = $app;
        $this->build = $build;
    }

    /**
     * {@inheritdoc}
     */
    public function withApp(AppContract $app): BuilderContract
    {
        $new = clone $this;
        $new->app = $app;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function app(): AppContract
    {
        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function withBuild(BuildContract $build): BuilderContract
    {
        $new = clone $this;
        $new->build = $build;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): BuildContract
    {
        return $this->build;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(RequestContract $request): BuilderContract
    {
        $new = clone $this;
        $new->request = $request;
        RequestInstance::set($request);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    /**
     * {@inheritdoc}
     */
    public function request(): RequestContract
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function withControllerName(string $controllerName): BuilderContract
    {
        $new = clone $this;
        $new->controllerName = $controllerName;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withControllerArguments(array $controllerArguments): BuilderContract
    {
        $new = clone $this;
        $new->controllerArguments = $controllerArguments;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasControllerArguments(): bool
    {
        return isset($this->controllerArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function controllerArguments(): array
    {
        return $this->controllerArguments;
    }
}
