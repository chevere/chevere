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

use Chevere\Parameter\FileParameter;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FileParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = string();
        $size = integer();
        $type = string();
        $tmp_name = string();
        $description = '';
        $default = [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => '',
            'size' => 0,
            'tmp_name' => '',
            'type' => '',
        ];
        $parameter = new FileParameter(
            $name,
            $type,
            $tmp_name,
            $size,
            $description,
        );
        $this->assertSame(null, $parameter->default());
        $this->assertSame(
            [0],
            $parameter->items()->getInteger('error')->accept()
        );
        $this->assertSame(
            $name,
            $parameter->items()->getString('name')
        );
        $this->assertSame(
            $size,
            $parameter->items()->getInteger('size')
        );
        $this->assertSame(
            $type,
            $parameter->items()->getString('type')
        );
    }

    public function testAssertCompatible(): void
    {
        $parameter = new FileParameter(
            name: string(),
            type: string(),
            tmp_name: string(),
            size: integer(),
        );
        $compatible = new FileParameter(
            string(),
            string(),
            string(),
            integer(),
        );
        $parameter->assertCompatible($compatible);
        $notCompatible = new FileParameter(
            name: string(),
            type: string(),
            tmp_name: string(),
            size: integer(accept: [1, 2, 3]),
        );
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
