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

use Chevere\Components\Route\Endpoint;
use Chevere\Components\Route\Tests\_resources\controllers\GetArticleController;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardsInterface;
use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcards;
use Chevere\Components\Route\RouteWildcardMatch;

return new class() extends Endpoint
{
    public function getController(): ControllerInterface
    {
        return new GetArticleController;
    }

    public function routeWildcards(): RouteWildcardsInterface
    {
        return new RouteWildcards(
            (new RouteWildcard('id'))
                ->withMatch(new RouteWildcardMatch('\d+'))
        );
    }
};
