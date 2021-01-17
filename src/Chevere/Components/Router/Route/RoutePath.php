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

namespace Chevere\Components\Router\Route;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Interfaces\Router\Route\RoutePathInterface;
use Chevere\Interfaces\Router\Route\WildcardsInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use Throwable;

final class RoutePath implements RoutePathInterface
{
    private string $route;

    private array $data;

    private RegexInterface $regex;

    private WildcardsInterface $wildcards;

    private string $name;

    public function __construct(string $route)
    {
        $std = new StrictStd();
        $this->data = $std->parse($route)[0];
        $dataGenerator = new DataGenerator();

        try {
            $dataGenerator->addRoute('GET', $this->data, '');
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                previous: $e,
                message: (new Message('Unable to add route %path%'))
                    ->code('%path%', $route),
            );
        }
        // @codeCoverageIgnoreEnd
        $this->setName();
        $this->route = $route;
        $this->wildcards = new Wildcards();
        $routerData = array_values(array_filter($dataGenerator->getData()));
        foreach ($this->data as $value) {
            if (! is_array($value)) {
                continue;
            }
            $this->wildcards = $this->wildcards
                ->withAddedWildcard(
                    new RouteWildcard($value[0], new RouteWildcardMatch($value[1]))
                );
        }
        $this->regex = new Regex(
            $routerData[0]['GET'][0]['regex'] ?? '#' . $route . '#'
        );
    }

    public function wildcards(): WildcardsInterface
    {
        return $this->wildcards;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->route;
    }

    private function setName(): void
    {
        $this->name = '';
        /**
         * @var string|string[] $el
         */
        foreach ($this->data as $el) {
            $this->name .= is_string($el)
                ? $el
                : '{' . $el[0] . '}';
        }
    }
}
