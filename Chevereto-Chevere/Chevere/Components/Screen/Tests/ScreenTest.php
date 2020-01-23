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

namespace Chevere\Components\Screen\Tests;

use Chevere\Components\Screen\Screen;
use PHPUnit\Framework\TestCase;

final class ScreenTest extends TestCase
{
    public function testConstruct(): void
    {
        // xdump($this);
        screen()->debug()->attachNl('Fatal error at: ' . __FILE__)->display();
        screen()->runtime()->attachNl(varInfo($this))->display();
        screen()->runtime()->attachNl('Everything is OK! Keep going...')->display();

        // $screen = new Screen;
        // $screen
        //     ->attachNl('pico')
        //     ->attachNl('pal')
        //     ->attachNl('q\'lee')
        //     ->display()
        //     ->attachNl(__FILE__)
        //     ->display();
    }
}
