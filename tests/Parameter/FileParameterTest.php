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
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\int;
use function Chevere\Parameter\string;

final class FileParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = string();
        $size = int();
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
            $parameter->parameters()->required('error')->int()->accept()
        );
        $this->assertSame(
            $name,
            $parameter->parameters()->required('name')->string()
        );
        $this->assertSame(
            $size,
            $parameter->parameters()->required('size')->int()
        );
        $this->assertSame(
            $type,
            $parameter->parameters()->required('type')->string()
        );
    }

    public function testAssertCompatible(): void
    {
        $parameter = new FileParameter(
            name: string(),
            type: string(),
            tmp_name: string(),
            size: int(),
        );
        $compatible = new FileParameter(
            string(),
            string(),
            string(),
            int(),
        );
        $parameter->assertCompatible($compatible);
        $notCompatible = new FileParameter(
            name: string(),
            type: string(),
            tmp_name: string(),
            size: int(accept: [1, 2, 3]),
        );
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
