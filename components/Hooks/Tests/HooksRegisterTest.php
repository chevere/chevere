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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\AssertHook;
use Chevere\Components\Hooks\HooksRegister;
use Error;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

final class HooksRegisterTest extends TestCase
{
    private DirInterface $tempDir;

    public function setUp(): void
    {
        $this->tempDir = (new Dir(new Path(__DIR__)))->getChild('_resources/temp');
        if ($this->tempDir->exists()) {
            $this->tempDir->removeContents();
        } else {
            $this->tempDir->create();
        }
    }

    public function tearDown(): void
    {
        $this->tempDir->removeContents();
    }

    public function testConstrut(): void
    {
        $hooksRegister = new HooksRegister();
        $this->assertSame([], $hooksRegister->hookablesClassMap());
    }

    public function testWithHookablesClassmap(): void
    {
        $hook = new MyHook();
        $hooksRegister = (new HooksRegister())
            ->withAddedHook(new AssertHook($hook))
            ->withHookablesClassMap($this->tempDir);
        $this->assertArrayHasKey($hook::hookableClassName(), $hooksRegister->hookablesClassMap());
    }
}
