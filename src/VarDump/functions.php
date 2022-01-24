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

namespace Chevere\VarDump {
    use Chevere\Throwable\Exceptions\LogicException;
    use Chevere\VarDump\Formats\VarDumpConsoleFormat;
    use Chevere\VarDump\Formats\VarDumpHtmlFormat;
    use Chevere\VarDump\Formats\VarDumpPlainFormat;
    use Chevere\VarDump\Interfaces\VarDumpInterface;
    use Chevere\VarDump\Outputs\VarDumpConsoleOutput;
    use Chevere\VarDump\Outputs\VarDumpHtmlOutput;
    use Chevere\VarDump\Outputs\VarDumpPlainOutput;
    use Chevere\Writer\Interfaces\WritersInterface;
    use function Chevere\Writer\streamFor;
    use Chevere\Writer\StreamWriter;
    use Chevere\Writer\Writers;
    use Chevere\Writer\WritersInstance;

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
    use function Chevere\VarDump\getVarDump;
    use function Chevere\VarDump\getVarDumpWriters;

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
