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
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Route\Tests\_resources\controllers\GetArticlesController;

return new class() extends Endpoint {
    public function controller(): ControllerInterface
    {
        return new GetArticlesController();
    }
};
