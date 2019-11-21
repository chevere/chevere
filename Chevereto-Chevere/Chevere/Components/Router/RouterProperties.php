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

namespace Chevere\Components\Router;

use Chevere\Contracts\Router\RouterPropertiesContract;

final class RouterProperties implements RouterPropertiesContract
{
    /** @var string Regex representation used when resolving routing */
    private $regex;

    /** @var array RouteContract members (objects serialized) [id => RouteContract] */
    private $routes;

    /** @var array Index route uri ['/path' => [id, 'route/key']] */
    private $index;

    /** @var array Group routes ['group' => [id,]] */
    private $groups;

    /** @var array Named routes ['name' => id] */
    private $named;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->regex = '';
        $this->routes = [];
        $this->index = [];
        $this->groups = [];
        $this->named = [];
    }

    /**
     * {@inheritdoc}
     */
    public function withRegex(string $regex): RouterPropertiesContract
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function regex(): string
    {
        return $this->regex;
    }

    /**
     * {@inheritdoc}
     */
    public function withRoutes(array $routes): RouterPropertiesContract
    {
        $new = clone $this;
        $new->routes = $routes;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function withIndex(array $index): RouterPropertiesContract
    {
        $new = clone $this;
        $new->index = $index;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function index(): array
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function withGroups(array $groups): RouterPropertiesContract
    {
        $new = clone $this;
        $new->groups = $groups;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function groups(): array
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function withNamed(array $named): RouterPropertiesContract
    {
        $new = clone $this;
        $new->named = $named;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function named(): array
    {
        return $this->named;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
