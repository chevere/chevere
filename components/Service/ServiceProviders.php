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

namespace Chevere\Components\Service;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\Service\ServiceableInterface;
use Chevere\Interfaces\Service\ServiceInterface;
use Chevere\Interfaces\Service\ServiceProvidersInterface;
use Ds\Map;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

final class ServiceProviders implements ServiceProvidersInterface
{
    use MapTrait;

    private ServiceableInterface $serviceable;

    private ReflectionMethod $reflectionMethod;

    public function __construct(ServiceableInterface $serviceable)
    {
        $this->serviceable = $serviceable;
        $this->map = new Map;
    }

    public function withAdded(string $method): ServiceProvidersInterface
    {
        $new = clone $this;
        try {
            $new->reflectionMethod = new ReflectionMethod($new->serviceable, $method);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                (new Message("Method %method% doesn't exists"))
                    ->code('%method%', $method),
                0,
                $e
            );
        }
        $new->assertVisibility();
        $new->assertParameterCount();
        $type = $new->getParameterType();
        $new->assertUnique();
        $new->map->put($method, $type);

        return $new;
    }

    private function assertVisibility(): void
    {
        if (!$this->reflectionMethod->isPublic()) {
            throw new LogicException(
                (new Message('Method %method% must be in the public scope'))
                    ->code('%method%', $this->reflectionMethod->getName())
            );
        }
    }

    private function assertParameterCount(): void
    {
        $parametersCount = $this->reflectionMethod->getNumberOfParameters();
        if ($parametersCount !== 1) {
            throw new ArgumentCountException(
                (new Message('Expecting exactly 1 argument, got %count%'))
                    ->code('%count%', (string) $parametersCount)
            );
        }
    }

    private function getParameterType(): string
    {
        /** @var ReflectionParameter $parameter */
        $parameter = $this->reflectionMethod->getParameters()[0];

        return $parameter->getType()->getName();
    }

    private function assertUnique(): void
    {
        $method = $this->reflectionMethod->getShortName();
        if ($this->map->hasKey($method)) {
            throw new OverflowException(
                (new Message('Method %method% has been already added'))
                    ->code('%method%', $method)
            );
        }
    }
}
