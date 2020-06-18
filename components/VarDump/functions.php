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

namespace Chevere\Components\VarDump {
    use Chevere\Components\Instances\VarDumpInstance;
    use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
    use Chevere\Components\VarDump\Formatters\HtmlFormatter;
    use Chevere\Components\VarDump\Formatters\PlainFormatter;
    use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
    use Chevere\Components\VarDump\Outputters\HtmlOutputter;
    use Chevere\Components\VarDump\Outputters\PlainOutputter;
    use Chevere\Components\VarDump\VarDump;
    use Chevere\Interfaces\VarDump\VarDumpInterface;

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpPlain(): VarDumpInterface
    {
        return
            new VarDump(
                new PlainFormatter,
                new PlainOutputter
            );
    }

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpConsole(): VarDumpInterface
    {
        return
            new VarDump(
                new ConsoleFormatter,
                new ConsoleOutputter
            );
    }

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpHtml(): VarDumpInterface
    {
        return
            new VarDump(
                new HtmlFormatter,
                new HtmlOutputter
            );
    }
}

namespace {
    use Chevere\Components\Instances\VarDumpInstance;
    use Chevere\Components\Instances\WritersInstance;
    use Chevere\Components\Writers\Writers;
    use function Chevere\Components\VarDump\getVarDumpConsole;

    if (function_exists('xd') === false) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to the output stream
         * @codeCoverageIgnore
         */
        function xd(...$vars)
        {
            try {
                $varDump = VarDumpInstance::get();
            } catch (LogicException $e) {
                $varDump = getVarDumpConsole();
            }
            try {
                $writers = WritersInstance::get();
            } catch (LogicException $e) {
                $writers = new Writers;
            }
            $varDump->withShift(1)->withVars(...$vars)->process($writers->out());
        }
    }
    if (function_exists('xdd') === false) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to the output stream and die()
         * @codeCoverageIgnore
         */
        function xdd(...$vars)
        {
            try {
                $varDump = VarDumpInstance::get();
            } catch (LogicException $e) {
                $varDump = getVarDumpConsole();
            }
            try {
                $writers = WritersInstance::get();
            } catch (LogicException $e) {
                $writers = new Writers;
            }
            $varDump->withShift(1)->withVars(...$vars)->process($writers->out());
            die(0);
        }
    }
}
