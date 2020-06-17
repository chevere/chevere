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
    use Chevere\Components\Writers\StreamWriterFromString;
    use Chevere\Exceptions\Core\LogicException;
    use Chevere\Interfaces\VarDump\VarDumpInterface;
    use Chevere\Interfaces\Writers\WriterInterface;

    const TYPE_PLAIN = 0;
    const TYPE_CONSOLE = 1;
    const TYPE_HTML = 2;

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
                (new Message('Missing %instance% instance (initiate it with %code%) or %fn%'))
                    ->strong('%instance%', VarDumpInstance::class)
                    ->code('%code%', 'new VarDumpInstance')
                    ->code('%fn%', 'setVarDump()')
            );
        }

        return $varDump->withVars(...$vars)->withShift(1);
    }

    /**
     * @codeCoverageIgnore
     */
    function setVarDump(int $enum = TYPE_CONSOLE, string $string = 'php://stdout', string $mode = 'w'): void
    {
        $writer = new StreamWriterFromString($string, $mode);
        switch ($enum) {
            case TYPE_PLAIN:
                    $varDump = getVarDumpPlain($writer);
                break;
            case TYPE_HTML:
                    $varDump = getVarDumpHtml($writer);
                break;
            case TYPE_CONSOLE:
            default:
                    $varDump = getVarDumpConsole($writer);
                break;
        }
        new VarDumpInstance($varDump);
    }
}

namespace {
    use Chevere\Components\Instances\VarDumpInstance;
    use function Chevere\Components\VarDump\setVarDump;
    use function Chevere\Components\VarDump\varDump;

    if (function_exists('xd') === false) { // @codeCoverageIgnore
        /**
         * Dumps information about one or more variables to the output stream
         * @codeCoverageIgnore
         */
        function xd(...$vars)
        {
            try {
                VarDumpInstance::get();
            } catch (LogicException $e) {
                setVarDump();
            }
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
            try {
                VarDumpInstance::get();
            } catch (LogicException $e) {
                setVarDump();
            }
            varDump(...$vars)->process();
            die(0);
        }
    }
}
