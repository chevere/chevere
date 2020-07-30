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

use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    public function testEmptyName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new Parameter('');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new Parameter(' ');
    }

    public function testSpaceInName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new Parameter('some name');
    }

    public function testConstruct(): void
    {
        $name = 'id';
        $regex = '/^[0-9+]$/';
        $controllerParameter = new Parameter('id');
        $this->assertSame($name, $controllerParameter->name());
        $this->assertSame($regex, $controllerParameter
            ->withRegex(new Regex($regex))->regex()->toString());
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $controllerParameter = new Parameter('test');
        $this->assertSame('', $controllerParameter->description());
        $controllerParameter = $controllerParameter->withDescription($description);
        $this->assertSame($description, $controllerParameter->description());
    }
}
