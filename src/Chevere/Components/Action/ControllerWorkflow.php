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

namespace Chevere\Components\Action;

use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowProviderInterface;

abstract class ControllerWorkflow extends Controller implements WorkflowProviderInterface
{
    protected WorkflowInterface $workflow;

    public function __construct()
    {
        parent::__construct();
        $this->workflow = $this->getWorkflow();
    }

    abstract function getWorkflow(): WorkflowInterface;

    public function workflow(): WorkflowInterface
    {
        return $this->workflow;
    }
}
