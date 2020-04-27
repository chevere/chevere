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
use PHPUnit\Framework\TestCase;

final class HooksRegisterTest extends TestCase
{
    private DirInterface $tempDir;

    private DirInterface $cacheDir;

    public function setUp(): void
    {
        $_resources = (new Dir(new Path(__DIR__ . '/')))->getChild('_resources/');
        $this->tempDir = $_resources->getChild('temp/');
        $this->cacheDir = $_resources->getChild('cache/');
        if ($this->tempDir->exists()) {
            $this->tempDir->removeContents();
        } else {
            $this->tempDir->create();
        }
        if (!$this->cacheDir->exists()) {
            $this->cacheDir->create();
        }
    }

    public function tearDown(): void
    {
        $this->tempDir->removeContents();
    }

    public function testConstrut(): void
    {
        $hooksRegister = new HooksRegister;
        $this->assertSame([], $hooksRegister->hookablesClassMap());
    }

    public function testWithHookablesClassmap(): void
    {
        $hook = new MyHook;
        $hooksRegister = (new HooksRegister)
            ->withAddedHook($hook)
            ->withHookablesClassMap($this->tempDir);
        $this->assertArrayHasKey($hook->className(), $hooksRegister->hookablesClassMap());
    }
}
