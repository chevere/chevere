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
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\Service\ServiceableInterface;
use Chevere\Interfaces\Service\ServiceInterface;
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

            private function withService(ServiceProvidersTestService $foo): self
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
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            public function withService($foo): self
            {
                $foo;

                return $this;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $serviceable->getServiceProviders();
    }

    public function testUnexpectedTyping(): void
    {
        $serviceable = new class implements ServiceableInterface
        {
            public function getServiceProviders(): ServiceProvidersInterface
            {
                return (new ServiceProviders($this))
                    ->withAdded('withService');
            }

            public function withService(ServiceInterface $foo): self
            {
                $foo;

                return $this;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $serviceable->getServiceProviders();
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

            public function withService(ServiceProvidersTestService $foo): self
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

final class ServiceProvidersTestService implements ServiceInterface
{
    public function getDescription(): string
    {
        return 'Does some';
    }

    public function some(string $foo, string $bar): void
    {
        $foo;
        $bar;
        // Pretend that I send an email here...
    }
}
