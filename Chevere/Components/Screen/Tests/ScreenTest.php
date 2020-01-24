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

use PHPUnit\Framework\TestCase;

final class ScreenTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        // xdump($this);
        // xdd(screen()->debug()->attach('Fatal error at: ' . __FILE__));
        screens()->debug()->attach('Fatal error at: ' . __FILE__)->show();
        // xdd(screens()->debug()->trace());
        // screens()->console()->attach('Fatal error at: ' . __FILE__)->show();
        // screen()->runtime()->attachNl(varInfo($this))->display();
        // screen()->runtime()->attachNl('Everything is OK! Keep going...')->display();

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
