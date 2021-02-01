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
    use Chevere\Components\VarDump\Formatters\VarDumpConsoleFormatter;
    use Chevere\Components\VarDump\Formatters\VarDumpHtmlFormatter;
    use Chevere\Components\VarDump\Formatters\VarDumpPlainFormatter;
    use Chevere\Components\VarDump\Outputters\VarDumpConsoleOutputter;
    use Chevere\Components\VarDump\Outputters\VarDumpHtmlOutputter;
    use Chevere\Components\VarDump\Outputters\VarDumpPlainOutputter;
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
                    new VarDumpPlainFormatter(),
                    new VarDumpPlainOutputter()
                );
    }

    function varDumpConsole(): VarDumpInterface
    {
        return
            new VarDump(
                new VarDumpConsoleFormatter(),
                new VarDumpConsoleOutputter()
            );
    }

    function varDumpHtml(): VarDumpInterface
    {
        return
            new VarDump(
                new VarDumpHtmlFormatter(),
                new VarDumpHtmlOutputter()
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

    if (function_exists('xd') === false) {
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
    if (function_exists('xdd') === false) {
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
