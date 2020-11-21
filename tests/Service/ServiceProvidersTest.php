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

namespace Chevere\Tests\Service;

use Chevere\Components\Service\ServiceProviders;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Service\ServiceableInterface;
use Chevere\Interfaces\Service\ServiceProvidersInterface;
use PHPUnit\Framework\TestCase;

final class ServiceProvidersTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }
        };
        $this->expectException(InvalidArgumentException::class);
        $serviceable->getServiceProviders();
    }

    public function testVisibility(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            private function withService(int $foo): self
            {
                $foo;

                return $this;
            }
        };
        $this->expectException(LogicException::class);
        $serviceable->getServiceProviders();
    }

    public function testArgumentCount(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            public function withService($foo, $bar, $twoThousand): self
            {
                $foo;
                $bar;
                $twoThousand;

                return $this;
            }
        };
        $this->expectException(ArgumentCountException::class);
        $serviceable->getServiceProviders();
    }

    public function testParameterTypes(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            private int $foo;

            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            public function withService(int $foo): self
            {
                $new = clone $this;
                $new->foo = $foo;

                return $new;
            }

            public function foo(): int
            {
                return $this->foo;
            }
        };
        $serviceable = $serviceable->withService(123);
        $this->assertSame(123, $serviceable->foo());
    }

    public function testOverflow(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            public function withService(int $foo): self
            {
                $foo;

                return $this;
            }
        };
        $this->expectException(OverflowException::class);
        $serviceable->getServiceProviders()
            ->withAdded('withService');
    }
}
