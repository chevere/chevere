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
use function Chevere\Parameter\arrayParameter;
use function Chevere\Parameter\generic;
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\GenericInterface;
use function Chevere\Parameter\stringParameter;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use PHPUnit\Framework\TestCase;

final class GenericTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameters = $this->getParameters();
        $parameters->assertHas(GenericInterface::GENERIC_NAME);
        $this->assertCount(1, $parameters);
        $this->assertSame(
            [GenericInterface::GENERIC_NAME],
            $parameters->required()
        );
        $this->assertSame([], $parameters->optional());
        $this->assertSame(
            $parameters->get(GenericInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithRequired(): void
    {
        $parameters = $this->getParameters();
        $parametersWith = $parameters->withAddedRequired($parameters->parameter());
        $this->assertNotSame($parameters, $parametersWith);
        $parametersWith->assertHas(GenericInterface::GENERIC_NAME);
        $this->assertSame([], $parametersWith->optional());
        $this->assertSame(
            [GenericInterface::GENERIC_NAME],
            $parametersWith->required()
        );
        $this->assertSame(
            $parametersWith->get(GenericInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithOptional(): void
    {
        $parameters = $this->getParameters();
        $parametersWith = $parameters->withAddedOptional($parameters->parameter());
        $this->assertNotSame($parameters, $parametersWith);
        $parametersWith->assertHas(GenericInterface::GENERIC_NAME);
        $this->assertSame([], $parametersWith->required());
        $this->assertSame(
            [GenericInterface::GENERIC_NAME],
            $parametersWith->optional()
        );
        $this->assertSame(
            $parametersWith->get(GenericInterface::GENERIC_NAME),
            $parameters->parameter()
        );
    }

    public function testWithRequiredBadTypePassed(): void
    {
        $this->expectException(TypeError::class);
        $this->getParameters()->withAddedRequired(
            stringParameter()
        );
    }

    public function testWithOptionalBadTypePassed(): void
    {
        $this->expectException(TypeError::class);
        $this->getParameters()->withAddedOptional(
            stringParameter()
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
        $parameters = generic(
            V: arrayParameter(
                id: integerParameter(),
                name: stringParameter(),
            ),
            K: integerParameter(minimum: 99)
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

    private function getParameters(): GenericInterface
    {
        return generic(
            V: arrayParameter()
        );
    }
}
