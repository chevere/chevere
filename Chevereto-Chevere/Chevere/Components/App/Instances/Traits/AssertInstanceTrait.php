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

namespace Chevere\Components\App\Instances\Traits;

use LogicException;

use Chevere\Components\Message\Message;

trait AssertInstanceTrait
{
    private static $instance;
  
    public static function assertInstance(): void
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                (new Message("Class %className% doesn't contain any %type% instance"))
                  ->code('%className%', __CLASS__)
                  ->code('%type%', self::type())
                  ->toString()
            );
        }
    }

    abstract public static function type();
}
