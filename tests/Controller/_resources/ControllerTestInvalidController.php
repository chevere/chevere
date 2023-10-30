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

namespace Chevere\Tests\Controller\_resources;

use Chevere\Controller\Controller;

final class ControllerTestInvalidController extends Controller
{
    protected function run(int $int): array
    {
        return [];
    }
}
