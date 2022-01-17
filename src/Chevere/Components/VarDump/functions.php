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

// @codeCoverageIgnoreStart

namespace Chevere\Components\VarDump {
    use Chevere\Components\VarDump\Format\VarDumpConsoleFormat;
    use Chevere\Components\VarDump\Format\VarDumpHtmlFormat;
    use Chevere\Components\VarDump\Format\VarDumpPlainFormat;
    use Chevere\Components\VarDump\Output\VarDumpConsoleOutput;
    use Chevere\Components\VarDump\Output\VarDumpHtmlOutput;
    use Chevere\Components\VarDump\Output\VarDumpPlainOutput;
    use function Chevere\Components\Writer\streamFor;
    use Chevere\Components\Writer\StreamWriter;
    use Chevere\Components\Writer\Writers;
    use Chevere\Components\Writer\WritersInstance;
    use Chevere\Exceptions\Core\LogicException;
    use Chevere\Interfaces\VarDump\VarDumpInterface;
    use Chevere\Interfaces\Writer\WritersInterface;

    function varDumpPlain(): VarDumpInterface
    {
        return
                new VarDump(
                    new VarDumpPlainFormat(),
                    new VarDumpPlainOutput()
                );
    }

    function varDumpConsole(): VarDumpInterface
    {
        return
            new VarDump(
                new VarDumpConsoleFormat(),
                new VarDumpConsoleOutput()
            );
    }

    function varDumpHtml(): VarDumpInterface
    {
        return
            new VarDump(
                new VarDumpHtmlFormat(),
                new VarDumpHtmlOutput()
            );
    }

    function getVarDump(): VarDumpInterface
    {
        try {
            return VarDumpInstance::get();
        } catch (LogicException $e) {
            return varDumpConsole();
        }
    }

    function getVarDumpWriters(): WritersInterface
    {
        try {
            return WritersInstance::get();
        } catch (LogicException $e) {
            return (new Writers())
                ->withOutput(
                    new StreamWriter(streamFor('php://stdout', 'r+'))
                )
                ->withError(
                    new StreamWriter(streamFor('php://stderr', 'r+'))
                );
        }
    }
}

namespace {
    use function Chevere\Components\VarDump\getVarDump;
    use function Chevere\Components\VarDump\getVarDumpWriters;

    if (!function_exists('xd')) {
        /**
         * Dumps information about one or more variables to the registered output writer stream
         */
        function xd(...$vars): void
        {
            getVarDump()
                ->withShift(1)
                ->withVars(...$vars)
                ->process(getVarDumpWriters()->output());
        }
    }
    if (!function_exists('xdd')) {
        /**
         * Dumps information about one or more variables to the registered output writer stream and die()
         * @codeCoverageIgnore
         */
        function xdd(...$vars): void
        {
            getVarDump()
                ->withShift(1)
                ->withVars(...$vars)
                ->process(getVarDumpWriters()->output());
            die(0);
        }
    }
}
// @codeCoverageIgnoreEnd
