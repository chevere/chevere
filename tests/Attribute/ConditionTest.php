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

use Chevere\Components\Attribute\Condition;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testCondition(): void
    {
        foreach ([false, true] as $bool) {
            $condition = new ConditionTestCondition($bool);
            $this->assertSame($bool, $condition->value());
            $this->assertSame(__FILE__, $condition->getDescription());
            $this->assertSame(
                str_replace('\\', '_', ConditionTestCondition::class),
                $condition->getIdentifier()
            );
        }
    }
}

final class ConditionTestCondition extends Condition
{
    public function getDescription(): string
    {
        return __FILE__;
    }
}
