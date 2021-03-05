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

namespace Chevere\Components\Controller;

use Chevere\Components\Workflow\Traits\WorkflowProviderTrait;
use Chevere\Interfaces\Workflow\WorkflowProviderInterface;

abstract class ControllerWorkflow extends Controller implements WorkflowProviderInterface
{
    use WorkflowProviderTrait;
}
