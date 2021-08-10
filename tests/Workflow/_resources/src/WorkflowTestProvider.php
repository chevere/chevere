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

use Chevere\Components\Workflow\Workflow;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowProviderInterface;

final class WorkflowTestProvider implements WorkflowProviderInterface
{
    public function getWorkflow(): WorkflowInterface
    {
        return new Workflow();
    }
}
