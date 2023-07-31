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
use Chevere\Parameter\Cast;
use Chevere\Parameter\Interfaces\CastInterface;
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

/**
 * @method mixed run()
 */
abstract class Action implements ActionInterface
{
    protected ?ParametersInterface $parameters = null;

    public static function acceptResponse(): ParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): CastInterface
    {
        $this::assert();
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $run = $this->run(...$arguments);

        try {
            assertArgument(static::acceptResponse(), $run);
        } catch (Throwable $e) {
            $message = message('%method% → %message%')
                ->withCode('%method%', static::runFQN())
                ->withCode('%exception%', $e::class)
                ->withTranslate('%message%', $e->getMessage());
            if (! ($e instanceof Exception)) {
                $message = $message->__toString();
            }

            throw new ($e::class)($message);
        }

        return new Cast($run);
    }

    final public static function assert(): void
    {
        static::assertMethod();
        $response = static::acceptResponse();
        $method = new ReflectionMethod(static::class, 'run');
        if (! $method->hasReturnType()) {
            if ($response->type()->typeHinting() === 'null') {
                return;
            }

            throw new TypeError(
                message('Method %method% must declare %type% return type')
                    ->withCode('%method%', static::runFQN())
                    ->withCode('%type%', $response->type()->typeHinting())
            );
        }
        /** @var ReflectionNamedType $returnType */
        $returnType = $method->getReturnType();
        static::assertTypes($returnType, $response);
        static::assertParameters();
    }

    public static function assertTypes(
        ReflectionNamedType $reflection,
        ParameterInterface $response
    ): void {
        $returnName = $reflection->getName();
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
                    ->withCode('%method%', static::runFQN())
                    ->withCode('%type%', implode('|', $expect))
            );
        }
    }

    public static function assertMethod(): void
    {
        if (! method_exists(static::class, 'run')) {
            throw new LogicException(
                message('Action %action% does not define run method')
                    ->withCode('%action%', static::class)
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected static function assertParameters(): void
    {
        // enables override
    }

    final protected function parameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            $this->parameters = getParameters(static::class);
        }

        return $this->parameters;
    }

    final protected static function runFQN(): string
    {
        return static::class . '::run';
    }
}
