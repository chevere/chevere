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

use Chevere\Components\Instances\VarDumpInstance;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\VarDump\VarDumpInterface;

/**
 * @codeCoverageIgnore
 */
function varDump(...$vars): VarDumpInterface
{
    try {
        $varDump = VarDumpInstance::get();
    } catch (LogicException $e) {
        throw new LogicException(
            (new Message('Missing %instance% instance (initiate it with %code%)'))
                ->strong('%instance%', VarDumpInstance::class)
                ->code('%code%', 'new VarDumpInstance')
        );
    }

    return $varDump->withVars(...$vars)->withShift(1);
}
if (function_exists('xd') === false) { // @codeCoverageIgnore
    /**
     * Dumps information about one or more variables to the output stream
     * @codeCoverageIgnore
     */
    function xd(...$vars)
    {
        varDump(...$vars)->stream();
    }
}
if (function_exists('xdd') === false) { // @codeCoverageIgnore
    /**
     * Dumps information about one or more variables to the output stream and die()
     * @codeCoverageIgnore
     */
    function xdd(...$vars)
    {
        varDump(...$vars)->stream();
        die(0);
    }
}
