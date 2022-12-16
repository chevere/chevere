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

namespace Chevere\Tests\DataStructure\Traits;

use Chevere\Tests\DataStructure\src\UsesVectorArrayTrait;
use PHPUnit\Framework\TestCase;

final class VectorToArrayTraitTest extends TestCase
{
    public function testConstruct(): void
    {
        $vector = new UsesVectorArrayTrait();
        $this->assertSame(['test'], $vector->toArray());
    }
}
