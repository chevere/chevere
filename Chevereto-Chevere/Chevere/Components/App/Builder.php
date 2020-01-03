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
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;

/**
 * The application builder container.
 */
final class Builder implements BuilderContract
{
    use ControllerNameAccessTrait;

    private BuildContract $build;

    private array $controllerArguments;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuildContract $build)
    {
        $this->build = $build;
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
    public function withControllerName(string $controllerName): BuilderContract
    {
        $new = clone $this;
        $new->controllerName = $controllerName;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withControllerArguments(array $arguments): BuilderContract
    {
        $new = clone $this;
        $new->controllerArguments = $arguments;

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
