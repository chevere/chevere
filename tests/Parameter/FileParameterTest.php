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
use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\stringParameter;
use PHPUnit\Framework\TestCase;

final class FileParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = stringParameter();
        $size = integerParameter();
        $type = stringParameter();
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
            $size,
            $type,
            $description,
        );
        $this->assertSame(
            $default,
            $parameter->default()
        );
        $this->assertSame(
            [0],
            $parameter->parameters()->getInteger('error')->accept()
        );
        $this->assertSame(
            $name,
            $parameter->parameters()->getString('name')
        );
        $this->assertSame(
            $size,
            $parameter->parameters()->getInteger('size')
        );
        $this->assertSame(
            $type,
            $parameter->parameters()->getString('type')
        );
    }
}
