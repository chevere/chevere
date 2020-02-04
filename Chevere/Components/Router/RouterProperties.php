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

namespace Chevere\Components\Router;

use Chevere\Components\Router\Properties\GroupsProperty;
use Chevere\Components\Router\Properties\IndexProperty;
use Chevere\Components\Router\Properties\NamedProperty;
use Chevere\Components\Router\Properties\RegexProperty;
use Chevere\Components\Router\Properties\RoutesProperty;
use Chevere\Components\Router\Interfaces\RouterPropertiesInterface;

final class RouterProperties implements RouterPropertiesInterface
{
    /** @var array RegexPropertyInterface::class[] */
    private array $classMap = [
        RegexProperty::class,
        // RoutesProperty::class,
        IndexProperty::class,
        GroupsProperty::class,
        NamedProperty::class,
    ];

    /** @var string */
    private string $regex;

    /** @var array */
    // private array $routes;

    /** @var array */
    private array $index;

    /** @var array */
    private array $groups;

    /** @var array */
    private array $named;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->regex = '';
        // $this->routes = [];
        $this->index = [];
        $this->groups = [];
        $this->named = [];
    }

    public function withRegex(string $regex): RouterPropertiesInterface
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    public function hasRegex(): bool
    {
        return '' != $this->regex;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    // public function withRoutes(array $routes): RouterPropertiesInterface
    // {
    //     $new = clone $this;
    //     $new->routes = $routes;

    //     return $new;
    // }

    // public function routes(): array
    // {
    //     return $this->routes;
    // }

    public function withIndex(array $index): RouterPropertiesInterface
    {
        $new = clone $this;
        $new->index = $index;

        return $new;
    }

    public function index(): array
    {
        return $this->index;
    }

    public function withGroups(array $groups): RouterPropertiesInterface
    {
        $new = clone $this;
        $new->groups = $groups;

        return $new;
    }

    public function groups(): array
    {
        return $this->groups;
    }

    public function withNamed(array $named): RouterPropertiesInterface
    {
        $new = clone $this;
        $new->named = $named;

        return $new;
    }

    public function named(): array
    {
        return $this->named;
    }

    public function assert(): void
    {
        foreach ($this->classMap as $className) {
            $prop = $className::NAME;
            new $className($this->{$prop});
        }
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->classMap as $className) {
            $prop = $className::NAME;
            $array[$prop] = $this->{$prop};
        }

        return $array;
    }
}
