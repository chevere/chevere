<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Symfony\Component\EventDispatcher\Event as EventBase;

class Event extends EventBase
{
    const NAME = 'demo.event';
    protected $foo;
 
    public function __construct()
    {
        $this->foo = 'bar';
    }
    public function getFoo()
    {
        return $this->foo;
    }
}
