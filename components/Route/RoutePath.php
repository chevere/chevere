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

namespace Chevere\Components\Route;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Interfaces\Route\RoutePathInterface;
use Chevere\Interfaces\Route\RouteWildcardsInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use Throwable;

final class RoutePath implements RoutePathInterface
{
    private string $path;

    private array $data;

    private RegexInterface $regex;

    private RouteWildcardsInterface $wildcards;

    public function __construct(string $path)
    {
        $std = new StrictStd;
        $this->data = $std->parse($path)[0];
        $this->path = $path;
        $this->wildcards = new RouteWildcards;
        $dataGenerator = new DataGenerator;
        try {
            $dataGenerator->addRoute('GET', $this->data, '');
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unable to add route %path%'))
                    ->code('%path%', $path),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
        $routerData = array_values(array_filter($dataGenerator->getData()));
        foreach ($this->data as $value) {
            if (!is_array($value)) {
                continue;
            }
            $this->wildcards = $this->wildcards
                ->withAddedWildcard(
                    new RouteWildcard($value[0], new RouteWildcardMatch($value[1]))
                );
        }
        $this->regex = new Regex($routerData[0]['GET'][0]['regex'] ?? '#' . $path . '#');
    }

    public function wildcards(): RouteWildcardsInterface
    {
        return $this->wildcards;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    public function toString(): string
    {
        return $this->path;
    }
}
