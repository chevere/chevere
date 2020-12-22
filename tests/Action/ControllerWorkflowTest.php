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

namespace Chevere\Tests\Action;

use Chevere\Components\Action\ControllerWorkflow;
use Chevere\Components\Workflow\Workflow;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use PHPUnit\Framework\TestCase;

final class ControllerWorkflowTest extends TestCase
{
    public function testConstruct(): void
    {
        $controller = new class() extends ControllerWorkflow {
            public function getWorkflow(): WorkflowInterface
            {
                return new Workflow('test');
            }

            public function run(ArgumentsInterface $arguments): ResponseSuccessInterface
            {
                return $this->getResponseSuccess([]);
            }
        };
        $this->assertNotSame($controller->getWorkflow(), $controller->workflow());
        $this->assertEqualsCanonicalizing(
            $controller->getWorkflow(),
            $controller->workflow()
        );
    }
}
