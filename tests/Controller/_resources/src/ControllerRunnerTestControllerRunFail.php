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

namespace Chevere\Tests\Controller\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Exception;

final class ControllerRunnerTestControllerRunFail extends Controller
{
    public function run(array $arguments): ResponseSuccessInterface
    {
        throw new Exception('Something went wrong');
    }
}