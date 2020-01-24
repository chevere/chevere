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

namespace Chevere\Tests\VarDump\Wrappers;

use Chevere\Components\VarDump\Interfaces\PalleteInterface;
use Chevere\Components\VarDump\Wrappers\ConsoleWrapper;
use Chevere\Components\VarDump\Wrappers\HtmlWrapper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HtmlWrapperTest extends TestCase
{
    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HtmlWrapper('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        $keys = array_keys(PalleteInterface::HTML);
        foreach ($keys as $key) {
            $wrapper = new HtmlWrapper($key);
            $wrapped = $wrapper->wrap($dump);
            $this->assertTrue(strlen($wrapped) > strlen($dump));
        }
    }
}
