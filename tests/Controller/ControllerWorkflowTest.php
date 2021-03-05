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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\ControllerWorkflow;
use Chevere\Components\Workflow\Workflow;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use PHPUnit\Framework\TestCase;

final class ControllerWorkflowTest extends TestCase
{
    public function testNoWorkflow(): void
    {
        $this->expectException(LogicException::class);
        $this->getController()->assertWorkflow();
    }

    public function testConstruct(): void
    {
        $controller = $this->getController();
        $workflow = $controller->getWorkflow();
        $controller = $controller->withWorkflow($workflow);
        $controller->assertWorkflow();
        $this->assertSame($workflow, $controller->workflow());
        $this->assertEqualsCanonicalizing(
            $workflow,
            $controller->workflow()
        );
    }

    private function getController(): ControllerWorkflow
    {
        return new class() extends ControllerWorkflow {
            public function getWorkflow(): WorkflowInterface
            {
                return new Workflow();
            }

            public function run(ArgumentsInterface $arguments): ResponseInterface
            {
                return $this->getResponse();
            }
        };
    }
}
