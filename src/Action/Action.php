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
use Chevere\Parameter\CastArgument;
use Chevere\Parameter\Interfaces\CastArgumentInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exception;
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionMethod;
use ReflectionNamedType;
use Throwable;
use function Chevere\Message\message;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;
use function Chevere\Type\toStandardType;

/**
 * @method mixed run()
 */
abstract class Action implements ActionInterface
{
    public const RUN_METHOD = 'run';

    protected ?ParametersInterface $parameters = null;

    public static function acceptResponse(): ParameterInterface
    {
        return arrayp();
    }

    final public static function assert(): void
    {
        static::assertMethod();
        static::assertStatic();
    }

    final public function getResponse(mixed ...$argument): CastArgumentInterface
    {
        static::assert();
        $this->assertRuntime();
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $run = $this->run(...$arguments);

        try {
            assertArgument(static::acceptResponse(), $run);
        } catch (Throwable $e) {
            $message = message('%method% → %message%')
                ->withCode('%method%', static::runMethodFQN())
                ->withCode('%exception%', $e::class)
                ->withTranslate('%message%', $e->getMessage());
            if (! ($e instanceof Exception)) {
                $message = $message->__toString();
            }

            throw new ($e::class)($message);
        }

        return new CastArgument($run);
    }

    public static function assertTypes(
        ReflectionNamedType $reflection,
        ParameterInterface $response
    ): void {
        $returnName = toStandardType($reflection->getName());
        $expectName = $response->type()->typeHinting();
        $return = match ($returnName) {
            'void' => 'null',
            'ArrayAccess' => 'array',
            default => $returnName,
        };
        $expect = [];
        if ($response instanceof UnionParameterInterface) {
            foreach ($response->parameters() as $parameter) {
                $expect[] = $parameter->type()->typeHinting();
            }
        } else {
            $expect[] = match ($expectName) {
                'generic' => 'array',
                default => $expectName,
            };
        }
        if (! in_array($return, $expect, true)) {
            throw new TypeError(
                message('Method %method% must declare %type% return type')
                    ->withCode('%method%', static::runMethodFQN())
                    ->withCode('%type%', implode('|', $expect))
            );
        }
    }

    protected static function assertMethod(): void
    {
        if (! method_exists(static::class, static::RUN_METHOD)) {
            throw new LogicException(
                message('Action %action% does not define %invoke% method')
                    ->withTranslate('%invoke%', static::RUN_METHOD)
                    ->withCode('%action%', static::class)
            );
        }
        $response = static::acceptResponse();
        $method = new ReflectionMethod(static::class, static::RUN_METHOD);
        if (! $method->hasReturnType()) {
            if ($response->type()->typeHinting() === 'null') {
                return;
            }

            throw new TypeError(
                message('Method %method% must declare %type% return type')
                    ->withCode('%method%', static::runMethodFQN())
                    ->withCode('%type%', $response->type()->typeHinting())
            );
        }
        /** @var ReflectionNamedType $returnType */
        $returnType = $method->getReturnType();
        static::assertTypes($returnType, $response);
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected static function assertStatic(): void
    {
        // enables extra static assertion
    }

    /**
     * Enables to define extra parameter assertion before the run method is called.
     * @codeCoverageIgnore
     */
    protected function assertRuntime(): void
    {
        // enables extra runtime assertion
    }

    final protected function parameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            $this->parameters = getParameters(static::class);
        }

        return $this->parameters;
    }

    final protected static function runMethodFQN(): string
    {
        return static::class . '::' . static::RUN_METHOD;
    }
}
