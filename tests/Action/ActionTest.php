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

use Chevere\Components\Action\Action;
use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction;
        $this->assertSame('test', $action->description());
        $this->assertCount(0, $action->parameters());
        $this->assertCount(1, $action->returnTypes());
        $this->assertArrayHasKey('id', $action->returnTypes());
        $arguments = new Arguments($action->parameters(), []);
        $action->run($arguments);
    }

    public function testBoundsAssertReturnTypes(): void
    {
        $this->expectException(OutOfBoundsException::class);
        (new ActionTestAction)->assertReturnTypes(['eee' => 123]);
    }

    public function testTypeAssertReturnTypes(): void
    {
        $this->expectException(TypeException::class);
        (new ActionTestAction)->assertReturnTypes(['id' => 'string']);
    }
}

final class ActionTestAction extends Action
{
    public function getDescription(): string
    {
        return 'test';
    }

    public function getReturnTypes(): array
    {
        return [
            'id' => new Type(Type::INTEGER),
        ];
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        $response = new ResponseSuccess([
            'id' => 123,
        ]);
        $this->assertReturnTypes($response->data());

        return $response;
    }
}
