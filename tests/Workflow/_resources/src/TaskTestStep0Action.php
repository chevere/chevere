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

namespace Chevere\Tests\Workflow\_resources\src;

use Chevere\Components\Action\Action;
use Chevere\Interfaces\Response\ResponseSuccessInterface;

class TaskTestStep0Action extends Action
{
    public function run(array $arguments): ResponseSuccessInterface
    {
        return $this->getResponseSuccess([]);
    }
}