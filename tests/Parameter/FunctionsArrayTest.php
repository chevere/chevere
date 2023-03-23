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

use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\integerp;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayTest extends TestCase
{
    public function testArrayEmpty(): void
    {
        $array = arrayp();
        assertArray($array, []);
        $this->expectException(ArgumentCountError::class);
        assertArray($array, [[]]);
    }

    public function testArrayFixed(): void
    {
        $array = arrayp(a: integerp());
        assertArray($array, [
            'a' => 1,
        ]);
        $this->expectException(ArgumentCountError::class);
        assertArray($array, []);
    }
}
