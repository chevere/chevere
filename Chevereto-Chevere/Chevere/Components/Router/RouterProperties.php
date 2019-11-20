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
    /** @var string Regex representation, used when resolving routing */
    private $regex;

    /** @var array Route members (objects, serialized) [id => Route] */
    private $routes;

    /** @var array Contains ['/path' => [id, 'route/key']] */
    private $index;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->regex = '';
        $this->routes = [];
        $this->index = [];
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
}
