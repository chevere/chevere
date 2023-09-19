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

namespace Chevere\Tests\Action\src;

use ArrayAccess;
use ArrayObject;
use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayp;

final class ActionTestArrayAccessReturnType extends Action
{
    public static function acceptResponse(): ParameterInterface
    {
        return arrayp();
    }

    protected function run(): ArrayAccess
    {
        return new ArrayObject([]);
    }
}