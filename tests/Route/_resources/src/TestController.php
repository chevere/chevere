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

namespace Chevere\Tests\Route\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerResponseSuccess;
use Chevere\Interfaces\Parameter\ArgumentedInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;

final class TestController extends Controller
{
    public function run(ArgumentedInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponseSuccess([]);
    }
}
