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

namespace Chevere\Tests\Routing\_resources\controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;

final class GetArticlesController extends Controller
{
    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
