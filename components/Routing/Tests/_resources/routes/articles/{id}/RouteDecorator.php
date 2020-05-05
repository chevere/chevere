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

use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardsInterface;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcardMatch;
use Chevere\Components\Route\RouteWildcards;

return new class() extends RouteDecorator
{
    public function name(): RouteNameInterface
    {
        return new RouteName('article-entity');
    }

    public function wildcards(): RouteWildcardsInterface
    {
        return (new RouteWildcards)
            ->withAddedWildcard(
                (new RouteWildcard('id'))
                    ->withMatch(new RouteWildcardMatch('\d+'))
            );
    }
};
