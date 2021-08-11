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

namespace Chevere\Tests\Workflow;

use Chevere\Components\Workflow\Step;
use function Chevere\Components\Workflow\step;
use Chevere\Components\Workflow\Steps;
use function Chevere\Components\Workflow\workflow;
use Chevere\Components\Workflow\Workflow;
use Chevere\Tests\Action\_resources\src\ActionTestAction;
use PHPUnit\Framework\TestCase;

final class WorkflowFunctionsTest extends TestCase
{
    public function testFunctionWorkflow(): void
    {
        $workflow = workflow();
        $this->assertEquals(new Workflow(new Steps()), $workflow);
    }

    public function testFunctionStep(): void
    {
        $args = ['action' => ActionTestAction::class];
        $step = step(...$args);
        $this->assertEquals(new Step(...$args), $step);
    }
}
