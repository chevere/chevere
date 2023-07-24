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
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionMethod;
use ReflectionNamedType;
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
        assertArgument(static::acceptResponse(), $run);

        return new Cast($run);
    }

    final public static function assert(): void
    {
        if (! method_exists(static::class, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->withCode('%action%', static::class)
            );
        }
        $response = static::acceptResponse();
        $reflection = new ReflectionMethod(static::class, 'run');
        if (! $reflection->hasReturnType()
            && $response->type()->typeHinting() === 'null') {
            return;
        }
        /** @var ReflectionNamedType $reflectionType */
        $reflectionType = $reflection->getReturnType();
        $returnName = $reflectionType->getName();
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
                    ->withCode('%method%', static::class . '::run')
                    ->withCode('%type%', $response->type()->typeHinting())
            );
        }
        static::assertRunParameters();
    }

    /**
     * @codeCoverageIgnore
     */
    protected static function assertRunParameters(): void
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
}
