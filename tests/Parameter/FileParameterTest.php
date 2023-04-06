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
use function Chevere\Parameter\integerp;
use function Chevere\Parameter\stringp;
use PHPUnit\Framework\TestCase;

final class FileParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = stringp();
        $size = integerp();
        $type = stringp();
        $tmp_name = stringp();
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
        $this->assertSame(
            null,
            $parameter->default()
        );
        $this->assertSame(
            [0],
            $parameter->parameters()->get('error')->accept()
        );
        $this->assertSame(
            $name,
            $parameter->parameters()->get('name')
        );
        $this->assertSame(
            $size,
            $parameter->parameters()->get('size')
        );
        $this->assertSame(
            $type,
            $parameter->parameters()->get('type')
        );
    }
}
