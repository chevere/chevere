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

use Chevere\Action\Attributes\Strict;
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ErrorException;
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use function Chevere\Attribute\getAttribute;
use function Chevere\Message\message;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\methodParameters;

/**
 * @method array<string, mixed> run()
 */
abstract class Action implements ActionInterface
{
    protected ?ParametersInterface $parameters = null;

    final public function assert(): void
    {
        $this->assertRunMethod();
        $this->parameters();
        $this->assertRunParameters();
    }

    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): ResponseInterface
    {
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $data = $this->run(...$arguments);
        $reflection = new ReflectionClass(static::class);
        /** @var Strict $strict */
        $strict = getAttribute($reflection, Strict::class);
        if ($strict->value) {
            /** @var array<string, mixed> $data */
            $data = assertArgument(static::acceptResponse(), $data);
        }

        return new Response(...$data);
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
        if (! $reflection->isProtected()) {
            throw new LogicException(
                message('Method %method% must be protected')
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

    /**
     * @codeCoverageIgnore
     */
    protected function assertRunParameters(): void
    {
        // enables override
    }

    final protected function parameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            $this->parameters = methodParameters(static::class, 'run');
        }

        return $this->parameters;
    }
}
