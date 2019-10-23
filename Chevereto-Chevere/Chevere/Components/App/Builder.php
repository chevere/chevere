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

use Chevere\Components\Controller\Traits\ControllerNameAccessTrait;
use Chevere\Components\Runtime\Runtime;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Http\RequestContract;

/**
 * Builder is responsible of building the application.
 *
 * The resulting build could be built from cache or from scratch.
 */
final class Builder implements BuilderContract
{
    use ControllerNameAccessTrait;

    /** @var AppContract */
    private $app;

    /** @var BuildContract */
    private $build;

    /** @var Runtime */
    private static $runtime;

    /** @var RequestContract */
    private static $request;

    /** @var array */
    private $controllerArguments;

    public function __construct(AppContract $app, BuildContract $build)
    {
        $this->app = $app;
        $this->build = $build;
    }

    public function withApp(AppContract $app): BuilderContract
    {
        $new = clone $this;
        $new->app = $app;

        return $new;
    }

    public function withBuild(BuildContract $build): BuilderContract
    {
        $new = clone $this;
        $new->build = $build;

        return $new;
    }

    public function withRequest(RequestContract $request): BuilderContract
    {
        $new = clone $this;
        $new::$request = $request;

        return $new;
    }

    public function withControllerName(string $controllerName): BuilderContract
    {
        $new = clone $this;
        $new->controllerName = $controllerName;

        return $new;
    }

    public function withControllerArguments(array $controllerArguments): BuilderContract
    {
        $new = clone $this;
        $new->controllerArguments = $controllerArguments;

        return $new;
    }

    public function hasRequest(): bool
    {
        return isset($this::$request);
    }

    public function hasControllerArguments(): bool
    {
        return isset($this->controllerArguments);
    }

    public function controllerArguments(): array
    {
        return $this->controllerArguments;
    }

    public function app(): AppContract
    {
        return $this->app;
    }

    public function build(): BuildContract
    {
        return $this->build;
    }

    public static function runtimeInstance(): Runtime
    {
        return self::$runtime;
    }

    /**
     * {@inheritdoc}
     */
    public static function requestInstance(): RequestContract
    {
        return self::$request;
    }

    /**
     * {@inheritdoc}
     */
    public static function setRuntimeInstance(Runtime $runtime)
    {
        self::$runtime = $runtime;
    }
}
