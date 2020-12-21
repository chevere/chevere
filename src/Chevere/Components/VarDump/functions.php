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
    use Chevere\Interfaces\VarDump\VarDumpInterface;

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
}

namespace {
    use Chevere\Components\Instances\VarDumpInstance;
    use Chevere\Components\Instances\WritersInstance;
    use function Chevere\Components\VarDump\varDumpConsole;
    use function Chevere\Components\Writer\streamFor;
    use Chevere\Components\Writer\StreamWriter;
    use Chevere\Components\Writer\Writers;

    if (function_exists('xd') === false) {
        /**
         * Dumps information about one or more variables to the output stream
         */
        function xd(...$vars): void
        {
            try {
                $varDump = VarDumpInstance::get();
            } catch (LogicException $e) {
                $varDump = varDumpConsole();
            }

            try {
                $writers = WritersInstance::get();
            } catch (LogicException $e) {
                $writers = (new Writers())
                    ->withOut(
                        new StreamWriter(streamFor('php://stdout', 'r+'))
                    )
                    ->withError(
                        new StreamWriter(streamFor('php://stderr', 'r+'))
                    );
            }
            $varDump->withShift(1)->withVars(...$vars)->process($writers->out());
        }
    }
    if (function_exists('xdd') === false) {
        /**
         * Dumps information about one or more variables to the output stream and die()
         * @codeCoverageIgnore
         */
        function xdd(...$vars): void
        {
            try {
                $varDump = VarDumpInstance::get();
            } catch (LogicException $e) {
                $varDump = varDumpConsole();
            }

            try {
                $writers = WritersInstance::get();
            } catch (LogicException $e) {
                $writers = (new Writers())
                    ->withOut(
                        new StreamWriter(streamFor('php://stdout', 'r+'))
                    )
                    ->withError(
                        new StreamWriter(streamFor('php://stderr', 'r+'))
                    );
            }
            $varDump->withShift(1)->withVars(...$vars)->process($writers->out());
            die(0);
        }
    }
}
// @codeCoverageIgnoreEnd
