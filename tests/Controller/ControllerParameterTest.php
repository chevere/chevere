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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\ControllerParameter;
use Chevere\Exceptions\Controller\ControllerParameterNameInvalidException;
use PHPUnit\Framework\TestCase;

final class ControllerParameterTest extends TestCase
{
    public function testEmptyName(): void
    {
        $this->expectException(ControllerParameterNameInvalidException::class);
        new ControllerParameter('');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(ControllerParameterNameInvalidException::class);
        new ControllerParameter(' ');
    }

    public function testSpaceInName(): void
    {
        $this->expectException(ControllerParameterNameInvalidException::class);
        new ControllerParameter('some name');
    }

    public function testConstruct(): void
    {
        $name = 'id';
        $regex = '/^[0-9+]$/';
        $controllerParameter = new ControllerParameter('id');
        $this->assertSame($name, $controllerParameter->name());
        $this->assertSame($regex, $controllerParameter->withRegex($regex)->regex()->toString());
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $controllerParameter = new ControllerParameter('test');
        $this->assertSame('', $controllerParameter->description());
        $controllerParameter = $controllerParameter->withDescription($description);
        $this->assertSame($description, $controllerParameter->description());
    }
}
