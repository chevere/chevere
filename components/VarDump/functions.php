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
    use Chevere\Components\Message\Message;
    use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
    use Chevere\Components\VarDump\Formatters\HtmlFormatter;
    use Chevere\Components\VarDump\Formatters\PlainFormatter;
    use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
    use Chevere\Components\VarDump\Outputters\HtmlOutputter;
    use Chevere\Components\VarDump\Outputters\PlainOutputter;
    use Chevere\Components\VarDump\VarDump;
    use Chevere\Exceptions\Core\LogicException;
    use Chevere\Interfaces\VarDump\VarDumpInterface;
    use Chevere\Interfaces\Writers\WriterInterface;

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpPlain(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new PlainFormatter,
                new PlainOutputter
            );
    }

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpConsole(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new ConsoleFormatter,
                new ConsoleOutputter
            );
    }

    /**
     * @codeCoverageIgnore
     */
    function getVarDumpHtml(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new HtmlFormatter,
                new HtmlOutputter
            );
    }

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
}

namespace {
    use function Chevere\Components\VarDump\varDump;

    if (function_exists('xd') === false) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to the output stream
         * @codeCoverageIgnore
         */
        function xd(...$vars)
        {
            varDump(...$vars)->process();
        }
    }
    if (function_exists('xdd') === false) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to the output stream and die()
         * @codeCoverageIgnore
         */
        function xdd(...$vars)
        {
            varDump(...$vars)->process();
            die(0);
        }
    }
}
