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

namespace Chevere\Tests\Job;

use Chevere\Components\Job\Job;
use Chevere\Components\Job\Task;
use PHPUnit\Framework\TestCase;

final class JobTest extends TestCase
{
    public function testTookOurJobs(): void
    {
        $this->expectNotToPerformAssertions();
        $job = (new Job('user-upload-image'))
            ->with(
                new Task(
                    'validate',
                    'validateImageFn',
                    ['${job:filename}']
                )
            )
            ->with(
                new Task(
                    'upload',
                    'uploadImageFn',
                    ['${job:filename}']
                )
            )
            ->with(
                new Task(
                    'bind-user',
                    'bindImageToUserFn',
                    ['${upload:id}', '${job:userId}']
                )
            )
            ->with(
                new Task(
                    'response',
                    'picoConLaWea',
                    ['${upload:id}']
                )
            );
        $job = $job
            // Plugin: check banned hashes
            ->withBefore(
                'validate',
                new Task(
                    'vendor-ban-check',
                    'vendorPath/banCheck',
                    ['${job:filename}']
                )
            )
            // Plugin: sepia filter
            ->withAfter(
                'validate',
                new Task(
                    'vendor-sepia-filter',
                    'vendorPath/sepiaFilter',
                    ['${job:filename}']
                )
            );
    }
}
