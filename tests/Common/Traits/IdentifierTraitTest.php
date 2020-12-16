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

namespace Chevere\Tests\Common\Traits;

use Chevere\Components\Common\Traits\IdentifierTrait;
use Chevere\Tests\Common\_resources\src\UsesIdentifierTrait;
use PHPUnit\Framework\TestCase;

final class IdentifierTraitTest extends TestCase
{
    public function testUseTrait(): void
    {
        $object = new UsesIdentifierTrait();
        $identifier = str_replace('\\', '_', get_class($object));
        $this->assertSame($identifier, $object->getIdentifier());
    }

    public function testAnonUseTrait(): void
    {
        $anon = new class() {
            use IdentifierTrait;
        };
        $this->assertSame(get_class($anon), $anon->getIdentifier());
    }
}
