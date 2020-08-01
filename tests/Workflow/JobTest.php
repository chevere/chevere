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

namespace Chevere\Tests\Workflow;

use Chevere\Components\Workflow\Job;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class JobTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Job('$jo.b');
    }

    public function testConstruct(): void
    {
        $name = 'the-job-name';
        $job = new Job($name);
        $this->assertSame($name, $job->toString());
    }
}
