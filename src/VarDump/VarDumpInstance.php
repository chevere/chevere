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

namespace Chevere\VarDump;

use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\VarDump\Interfaces\VarDumpInterface;

final class VarDumpInstance
{
    private static ?VarDumpInterface $instance;

    public function __construct(VarDumpInterface $varDump)
    {
        self::$instance = $varDump;
    }

    public static function get(): VarDumpInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                new Message('No VarDump instance present')
            );
        }

        return self::$instance;
    }
}
