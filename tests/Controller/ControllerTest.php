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

use Chevere\Components\Pluggable\Plug\Hook\HooksQueue;
use Chevere\Components\Pluggable\Plug\Hook\HooksRunner;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Controller\_resources\src\ControllerTestController;
use Chevere\Tests\Controller\_resources\src\ControllerTestControllerDispatchAttribute;
use Chevere\Tests\Controller\_resources\src\ControllerTestControllerRelationAttribute;
use Chevere\Tests\Controller\_resources\src\ControllerTestControllerRelationWorkflowAttribute;
use Chevere\Tests\Controller\_resources\src\ControllerTestControllerRelationWorkflowAttributeError;
use Chevere\Tests\Controller\_resources\src\ControllerTestInvalidController;
use Chevere\Tests\Controller\_resources\src\ControllerTestModifyParamConflictHook;
use Chevere\Tests\Controller\_resources\src\ControllerTestModifyParamHook;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestProvider;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testConstructInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerTestInvalidController();
    }

    public function testControllerNoAttributes(): void
    {
        $controller = new ControllerTestController();
        $this->assertSame(Type::STRING, $controller->parameter()->type()->primitive());
        $this->assertSame('', $controller->relation());
        $this->assertSame('', $controller->dispatch());
    }

    public function testControllerDispatchAttribute(): void
    {
        $controller = new ControllerTestControllerDispatchAttribute();
        $this->assertSame('some', $controller->dispatch());
    }

    public function testControllerRelationAttribute(): void
    {
        $controller = new ControllerTestControllerRelationAttribute();
        $this->assertSame('test relation', $controller->relation());
    }

    public function testControllerRelationWorkflowAttribute(): void
    {
        $controller = new ControllerTestControllerRelationWorkflowAttribute();
        $this->assertSame(WorkflowTestProvider::class, $controller->relation());
    }

    public function testControllerRelationWorkflowAttributeError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerTestControllerRelationWorkflowAttributeError();
    }

    public function testHookedController(): void
    {
        $hook = new ControllerTestModifyParamHook();
        $hooksQueue = (new HooksQueue())
            ->withAdded($hook);
        $controller = new ControllerTestController();
        $controller = $controller->withHooksRunner(
            new HooksRunner($hooksQueue)
        );
        $this->assertCount(1, $controller->parameters());
        $this->assertSame(['string'], $controller->parameters()->keys());
    }

    public function testHookedControllerParamConflict(): void
    {
        $hook = new ControllerTestModifyParamConflictHook();
        $hooksQueue = (new HooksQueue())
            ->withAdded($hook);
        $controller = new ControllerTestController();
        $controller = $controller->withHooksRunner(
            new HooksRunner($hooksQueue)
        );
        $this->assertCount(1, $controller->parameters());
    }
}
