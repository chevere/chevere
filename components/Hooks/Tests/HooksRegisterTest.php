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
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\HooksRegister;
use PHPUnit\Framework\TestCase;

final class HooksRegisterTest extends TestCase
{
    private DirInterface $tempDir;

    public function setUp(): void
    {
        $_resources = (new DirFromString(__DIR__ . '/'))->getChild('_resources/');
        $this->tempDir = $_resources->getChild('temp/');
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
        $hooksRegister = new HooksRegister;
        $this->assertSame([], $hooksRegister->hookablesMap()->toArray());
    }

    public function testWithHookablesClassmap(): void
    {
        $hook = new MyHook;
        $hooksRegister = (new HooksRegister)
            ->withAddedHook($hook)
            ->withHookablesClassMap($this->tempDir);
        $this->assertTrue($hooksRegister->hookablesMap()->has($hook->className()));
    }
}
