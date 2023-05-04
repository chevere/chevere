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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;
use Chevere\Container\Container;
use function Chevere\Message\message;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ErrorException;
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionMethod;
use ReflectionNamedType;

abstract class Action implements ActionInterface
{
    use ActionTrait;

    public function __construct()
    {
        $this->onConstruct();
    }

    final protected function onConstruct(): void
    {
        $this->setUpBefore();
        $this->assertRunMethod();
        $this->parameters = $this->getParameters();
        $this->assertRunParameters();
        $this->acceptResponse = $this->acceptResponse();
        $this->setUpAfter();
    }

    /**
     * This method runs on class instantiation (before __construct).
     */
    protected function setUpBefore(): void
    {
        // enables override
    }

    /**
     * This method runs on class instantiation (after __construct).
     */
    protected function setUpAfter(): void
    {
        // enables override
    }

    // @infection-ignore-all
    protected function assertRunParameters(): void
    {
        // enables override
    }

    final protected function assertRunMethod(): void
    {
        if (! method_exists($this, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->withCode('%action%', $this::class)
            );
        }
        $reflection = new ReflectionMethod($this, 'run');
        $translate = [
            '%method%', $this::class . '::run',
        ];
        if (! $reflection->isPublic()) {
            throw new ErrorException(
                message('Method %method% must be public')
                    ->withTranslate(...$translate)
            );
        }
        if (! $reflection->hasReturnType()) {
            throw new ErrorException(
                message('Method %method% must declare array return type')
                    ->withTranslate(...$translate)
            );
        }
        /** @var ReflectionNamedType $reflectionType */
        $reflectionType = $reflection->getReturnType();
        if ($reflectionType->getName() !== 'array') {
            throw new TypeError(
                message('Method %method% must return an array')
                    ->withTranslate(...$translate)
            );
        }
    }
}
