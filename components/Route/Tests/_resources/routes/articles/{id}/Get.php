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
use Chevere\Components\Route\Interfaces\WildcardCollectionInterface;
use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\WildcardCollection;
use Chevere\Components\Route\WildcardMatch;

return new class() extends Endpoint {
    public function getController(): ControllerInterface
    {
        return new GetArticleController;
    }

    public function wildcardCollection(): WildcardCollectionInterface
    {
        return new WildcardCollection(
            (new Wildcard('id'))
                ->withMatch(new WildcardMatch('\d+'))
        );
    }
};
