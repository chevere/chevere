<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Serialize;

use Chevere\Components\Path\Path;
use Chevere\Components\Serialize\Serialize;
use PHPUnit\Framework\TestCase;

final class attach
{
    private $val;

    public function __construct($val)
    {
        $this->val = $val;
    }
}

final class SerializeTest extends TestCase
{
    // public function testConstruct(): void
    // {
    //     $object = ['eee', 'ppp', 5 => [3 => [new Path('eee')]], 12 => new attach(fopen(__FILE__, 'r'))];
    //     // $object = fopen(__FILE__, 'r');
    //     $serialized = new Serialize($object);
    //     dd($object, $serialized->toString());
    // }
}
