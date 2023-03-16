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

namespace Chevere\Tests\Parameter;

use Chevere\Parameter\Arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\genericp;
use Chevere\Parameter\Generics;
use function Chevere\Parameter\integerp;
use Chevere\Parameter\Interfaces\GenericsInterface;
use function Chevere\Parameter\stringp;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use PHPUnit\Framework\TestCase;

final class GenericsTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameters = $this->getParameters();
        $parameters->assertHas(GenericsInterface::GENERIC_NAME);
        $this->assertCount(1, $parameters);
        $this->assertSame(
            [GenericsInterface::GENERIC_NAME],
            $parameters->required()
        );
        $this->assertSame([], $parameters->optional());
        $this->assertSame(
            $parameters->get(GenericsInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithRequired(): void
    {
        $parameters = $this->getParameters();
        $parametersWith = $parameters->withAddedRequired($parameters->parameter());
        $this->assertNotSame($parameters, $parametersWith);
        $parametersWith->assertHas(GenericsInterface::GENERIC_NAME);
        $this->assertSame([], $parametersWith->optional());
        $this->assertSame(
            [GenericsInterface::GENERIC_NAME],
            $parametersWith->required()
        );
        $this->assertSame(
            $parametersWith->get(GenericsInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithOptional(): void
    {
        $parameters = $this->getParameters();
        $parametersWith = $parameters->withAddedOptional($parameters->parameter());
        $this->assertNotSame($parameters, $parametersWith);
        $parametersWith->assertHas(GenericsInterface::GENERIC_NAME);
        $this->assertSame([], $parametersWith->required());
        $this->assertSame(
            [GenericsInterface::GENERIC_NAME],
            $parametersWith->optional()
        );
        $this->assertSame(
            $parametersWith->get(GenericsInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithRequiredBadTypePassed(): void
    {
        $this->expectException(TypeError::class);
        $this->getParameters()->withAddedRequired(
            stringp()
        );
    }

    public function testWithOptionalBadTypePassed(): void
    {
        $this->expectException(TypeError::class);
        $this->getParameters()->withAddedOptional(
            stringp()
        );
    }

    public function testWithRequiredArgumentCount(): void
    {
        $parameters = $this->getParameters();
        $this->expectException(ArgumentCountError::class);
        $parameters->withAddedRequired($parameters->parameter(), $parameters->parameter());
    }

    public function testWithOptionalArgumentCount(): void
    {
        $parameters = $this->getParameters();
        $this->expectException(ArgumentCountError::class);
        $parameters->withAddedOptional($parameters->parameter(), $parameters->parameter());
    }

    public function testWea(): void
    {
        $parameters = new Generics(
            genericp(
                V: arrayp(
                    id: integerp(),
                    name: stringp(),
                ),
                K: integerp(minimum: 99)
            )
        );
        $weas = [
            100 => [
                'id' => 1,
                'name' => 'luis',
            ],
            200 => [
                'id' => 2,
                'name' => 'miguel',
            ],
        ];
        $this->expectNotToPerformAssertions();
        new Arguments($parameters, $weas);
    }

    private function getParameters(): GenericsInterface
    {
        return new Generics(
            genericp(
                V: arrayp()
            )
        );
    }
}
