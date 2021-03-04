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
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use PHPUnit\Framework\TestCase;

final class ControllerWorkflowTest extends TestCase
{
    public function testConstruct(): void
    {
        $controller = new class() extends ControllerWorkflow {
            public function getWorkflow(): WorkflowInterface
            {
                return new Workflow();
            }

            public function run(ArgumentsInterface $arguments): ResponseInterface
            {
                return $this->getResponse();
            }
        };
        $this->assertNotSame($controller->getWorkflow(), $controller->workflow());
        $this->assertEqualsCanonicalizing(
            $controller->getWorkflow(),
            $controller->workflow()
        );
    }
}
