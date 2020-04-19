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
use Chevere\Components\Controller\Exceptions\ControllerArgumentCountException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentKeyNotExistsException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexException;
use Chevere\Components\Controller\Parameter;
use Chevere\Components\Controller\Parameters;
use Chevere\Components\Regex\Regex;
use PHPUnit\Framework\TestCase;

final class ControllerArgumentsMakerTest extends TestCase
{
    public function testInvalidArgumentCount(): void
    {
        $parameters = new Parameters;

        $this->expectException(ControllerArgumentCountException::class);
        new ControllerArgumentsMaker($parameters, ['name' => 'value']);
    }

    public function testInvalidArgumentKey(): void
    {
        $parameters = (new Parameters)
            ->withParameter(
                new Parameter('id', new Regex('/^[0-9]+$/'))
            );

        $this->expectException(ControllerArgumentKeyNotExistsException::class);
        new ControllerArgumentsMaker($parameters, ['name' => 'value']);
    }

    public function testInvalidRegexValue(): void
    {
        $parameters = (new Parameters)
            ->withParameter(
                new Parameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ControllerArgumentRegexException::class);
        new ControllerArgumentsMaker($parameters, ['id' => 'abc']);
    }

    public function testConstruct(): void
    {
        $parameters = (new Parameters)
            ->withParameter(
                new Parameter('id', new Regex('/^[0-9]+$/'))
            )
            ->withParameter(
                new Parameter('name', new Regex('/^\w+$/'))
            );
        $arguments = [
            'id' => '123',
            'name' => 'PeterVeneno'
        ];
        $maker = new ControllerArgumentsMaker($parameters, $arguments);
        foreach ($arguments as $name => $value) {
            $this->assertTrue($maker->arguments()->hasKey($name));
            $this->assertSame($value, $maker->arguments()->get($name));
        }
    }
}
