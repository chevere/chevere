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

namespace Chevere\Tests\Action;

use Chevere\Action\ActionName;
use Chevere\Tests\Action\_resources\ActionTestAction;
use Chevere\Throwable\Errors\TypeError;
use PHPUnit\Framework\TestCase;

final class ActionNameTest extends TestCase
{
    public function testWrongInterface(): void
    {
        $this->expectException(TypeError::class);
        new ActionName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ActionTestAction::class;
        $actionName = new ActionName($className);
        $this->assertSame($className, $actionName->__toString());
    }
}
