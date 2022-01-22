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
use Chevere\Components\Router\Parsers\StrictStd;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Interfaces\Router\Route\RoutePathInterface;
use Chevere\Interfaces\Router\Route\RouteWildcardsInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use Throwable;

final class RoutePath implements RoutePathInterface
{
    private array $data;

    private RegexInterface $regex;

    private RouteWildcardsInterface $wildcards;

    private string $name;

    public function __construct(
        private string $route
    ) {
        $std = new StrictStd();
        $this->data = $std->parse($this->route)[0];
        $dataGenerator = new DataGenerator();

        try {
            $dataGenerator->addRoute('GET', $this->data, '');
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new LogicException(
                previous: $e,
                message: (new Message('Unable to add route %path%'))
                    ->code('%path%', $this->route),
            );
        }
        // @codeCoverageIgnoreEnd
        $this->setName();
        $this->wildcards = new RouteWildcards();
        $routerData = array_values(array_filter($dataGenerator->getData()));
        foreach ($this->data as $value) {
            if (!is_array($value)) {
                continue;
            }
            $this->wildcards = $this->wildcards
                ->withPut(
                    new RouteWildcard($value[0], new RouteWildcardMatch($value[1]))
                );
        }
        $this->regex = new Regex(
            $routerData[0]['GET'][0]['regex'] ?? '#' . $route . '#'
        );
    }

    public function wildcards(): RouteWildcardsInterface
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

    public function __toString(): string
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
