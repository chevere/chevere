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

namespace Chevere\Tests\Attribute;

use Chevere\Tests\Attribute\_resources\ClassUsesDescription;
use PHPUnit\Framework\TestCase;
use function Chevere\Attribute\getDescription;

final class FunctionsTest extends TestCase
{
    public function testClassDescription(): void
    {
        $symbol = ClassUsesDescription::class;
        $description = getDescription($symbol);
        $this->assertSame('Class', $description->__toString());
        $symbol = ClassUsesDescription::class . '::run()';
        $description = getDescription($symbol);
        $this->assertSame('Method', $description->__toString());
        $symbol = ClassUsesDescription::class . '::$property';
        $description = getDescription($symbol);
        $this->assertSame('Property', $description->__toString());
        $symbol = ClassUsesDescription::class . '::run($parameter)';
        $description = getDescription($symbol);
        $this->assertSame('Parameter', $description->__toString());
        $symbol = ClassUsesDescription::class . '::CONSTANT';
        $description = getDescription($symbol);
        $this->assertSame('Constant', $description->__toString());
    }

    public function testFunctionDescription(): void
    {
        new ClassUsesDescription();
        $symbol = 'Chevere\Tests\Attribute\_resources\functionUsesDescription';
        $description = getDescription($symbol);
        $this->assertSame('Function', $description->__toString());
        $symbol = 'Chevere\Tests\Attribute\_resources\functionUsesDescription($parameter)';
        $description = getDescription($symbol);
        $this->assertSame('Parameter', $description->__toString());
    }
}
