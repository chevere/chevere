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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\ControllerArgumentsMaker;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Exceptions\ControllerArgumentNameNotExistsException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexException;
use Chevere\Components\Regex\Regex;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ControllerArgumentsMakerTest extends TestCase
{
    public function testInvalidArgumentName(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );

        $this->expectException(OutOfBoundsException::class);
        (new ControllerArgumentsMaker($parameters))
            ->withBind('name', 'value');
    }

    public function testInvalidRegexValue(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ControllerArgumentRegexException::class);
        (new ControllerArgumentsMaker($parameters))
            ->withBind('id', 'abc');
    }

    public function testConstruct(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            )
            ->withParameter(
                new ControllerParameter('name', new Regex('/^\w+$/'))
            );
        $arguments = [
            'id' => '123',
            'name' => 'PeterVeneno'
        ];
        $maker = new ControllerArgumentsMaker($parameters);
        /**
         * @var string $name
         * @var string $value
         */
        foreach ($arguments as $name => $value) {
            $maker->withBind($name, $value);
            $this->assertTrue($maker->arguments()->has($name));
            $this->assertSame($value, $maker->arguments()->get($name));
        }
    }
}
