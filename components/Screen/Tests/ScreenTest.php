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
use function GuzzleHttp\Psr7\stream_for;

final class ScreenTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        // xdump($this);
        // xdump(screens()->debug()->attach('Fatal error at: ' . __FILE__));
        // xdd(screens()->debug()->trace());
        // screens()->console()->add('Fatal error at: ' . __FILE__)->emit();
        // screens()->runtime()->addNl(varInfo(screens()->console()))->emit();
        // screens()->runtime()->attachNl('Everything is OK! Keep going...')->emit();

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
