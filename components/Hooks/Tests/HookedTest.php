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
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\Exceptions\HooksClassNotRegisteredException;
use Chevere\Components\Hooks\Hooks;
use Chevere\Components\Instances\HooksInstance;
use PHPUnit\Framework\TestCase;

final class HookedTest extends TestCase
{
    private Hooks $hooks;

    public function setUp(): void
    {
        $resourcespath = (new Path(__DIR__ . '/'))->getChild('_resources/');
        $hookablesClassmap = $resourcespath->getChild('hookables_classmap.php');
        $hookables = include $hookablesClassmap->absolute();
        foreach ($hookables as $k => &$v) {
            $v = strtr($v, [
                '%hooksPath%' => (new Dir($resourcespath))->getChild('hooks/')
                    ->path()->absolute()
            ]);
        }
        $this->hooks = new Hooks($hookables);
        new HooksInstance($this->hooks);
        include_once 'MyHookable.php';
    }

    public function testWithoutHooksQueue(): void
    {
        $string = 'string';
        $myHookable = new MyHookableWithoutHooks();
        $myHookable->setString($string);
        $this->assertSame($string, $myHookable->string());
    }

    public function testHooked(): void
    {
        $string = 'string';
        /**
         * @var MyHookable $myHookable
         */
        $myHookable = (new MyHookable)
            ->withHooksRunner(HooksInstance::get()->getRunner(MyHookable::class));
        $myHookable->setString($string);
        $this->assertSame("(hooked $string)", $myHookable->string());
    }

    public function testNotHookedClass(): void
    {
        $string = 'string';
        $myHookable = new MyHookableWithNotRegisteredClass();
        $myHookable->setString($string);
        $this->assertSame($string, $myHookable->string());
    }

    public function testClassNotRegistered(): void
    {
        $this->expectException(HooksClassNotRegisteredException::class);
        $this->hooks->getRunner(MyHookableWithNotRegisteredClass::class);
    }
}
