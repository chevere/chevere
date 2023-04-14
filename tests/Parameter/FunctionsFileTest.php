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

use function Chevere\Parameter\assertFile;
use function Chevere\Parameter\file;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class FunctionsFileTest extends TestCase
{
    public function testArrayEmpty(): void
    {
        $parameter = file();
        $array = [
            'error' => 0,
            'name' => 'test.txt',
            'size' => 0,
            'tmp_name' => '/tmp/php/php7v5q0s',
            'type' => 'text/plain',
        ];
        $this->assertSame($array, assertFile($parameter, $array));
        $this->expectException(ArgumentCountError::class);
        assertFile($parameter, [[]]);
    }
}
