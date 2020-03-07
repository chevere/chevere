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

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\Tests\_resources\controllers\GetArticleController;

return new class() extends RouteEndpoint
{
    public function getController(): ControllerInterface
    {
        return new GetArticleController;
    }
};
