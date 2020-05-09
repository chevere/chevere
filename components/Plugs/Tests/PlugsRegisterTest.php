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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Hooks\Tests\_resources\TestHook;
use Chevere\Components\Plugs\PlugsRegister;
use PHPUnit\Framework\TestCase;

final class PlugsRegisterTest extends TestCase
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

    // public function testConstrut(): void
    // {
    //     $plugsRegister = new PlugsRegister;
    //     $this->assertSame([], $plugsRegister->classMap()->toArray());
    // }

    // public function testWithHookablesClassmap(): void
    // {
    //     $hook = new TestHook;
    //     $hooksRegister = (new PlugsRegister)
    //         ->withAddedPlug($hook)
    //         ->withClassMapAt($this->tempDir);
    //     $this->assertTrue($hooksRegister->classMap()->has($hook->at()));
    // }
}
