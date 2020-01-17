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

namespace Chevere\Components\ExceptionHandler;

use Chevere\Components\App\App;
use Chevere\Components\ExceptionHandler\Interfaces\TraceInterface;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use Chevere\Components\ExceptionHandler\Interfaces\TraceEntryInterface;
use Chevere\Components\VarDump\Dumpeable;
use Chevere\Components\VarDump\VarDump;

final class Trace implements TraceInterface
{
    private array $trace;

    private FormatterInterface $formatter;

    private array $array = [];

    private string $string = '';

    /**
     * Creates a new instance.
     */
    public function __construct(array $trace, FormatterInterface $formatter)
    {
        $this->trace = $trace;
        $this->formatter = $formatter;
        foreach ($this->trace as $pos => $entry) {
            $this->array[] = strtr(
                $this->formatter->getTraceEntryTemplate(),
                $this->getTrTable($pos, new TraceEntry($entry))
            );
        }
        $this->string = $this->wrapStringHr($this->glueString($this->array));
    }

    /**
     * {@inheritdoc}
     *
     * @return array Containing the formatter trace entries
     */
    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * {@inheritdoc}
     *
     * @return string Containing the formatter trace entries as string ready to screen.
     */
    public function toString(): string
    {
        return $this->string;
    }

    private function getTrTable(int $pos, TraceEntryInterface $entry): array
    {
        $trValues = [
            TraceInterface::TAG_ENTRY_CSS_EVEN_CLASS => ($pos & 1) ? 'entry--even' : '',
            TraceInterface::TAG_ENTRY_POS => $pos,
            TraceInterface::TAG_ENTRY_FILE => $entry->file(),
            TraceInterface::TAG_ENTRY_LINE => $entry->line(),
            TraceInterface::TAG_ENTRY_FILE_LINE => $entry->fileLine(),
            TraceInterface::TAG_ENTRY_CLASS => $entry->class(),
            TraceInterface::TAG_ENTRY_TYPE => $entry->type(),
            TraceInterface::TAG_ENTRY_FUNCTION => $entry->function(),
        ];
        $array = $trValues;
        foreach (TraceInterface::HIGHLIGHT_TAGS as $tag => $key) {
            $val = $trValues[$tag];
            if (empty($val)) {
                continue;
            }
            $array[$tag] = $this->formatter->varDumpFormatter()->applyWrap($key, (string) $trValues[$tag]);
        }
        $array[TraceInterface::TAG_ENTRY_ARGUMENTS] = $this->getEntryArguments($entry);

        return $array;
    }

    private function getEntryArguments(TraceEntryInterface $entry): string
    {
        $string = '';
        foreach ($entry->args() as $pos => $var) {
            $string .= "\n";
            $aux = 'Arg#' . ($pos + 1) . ' ';
            $varDump = (new VarDump(new Dumpeable($var), $this->formatter->varDumpFormatter()))
                ->withDontDump(App::class)
                ->withProcess();
            $string .= $aux . $varDump->toString() . "\n";
        }
        $string = rtrim($string, "\n");

        return $string;
    }

    private function wrapStringHr(string $text): string
    {
        return $this->formatter->getHr() . "\n" . $text . "\n" . $this->formatter->getHr();
    }

    private function glueString(array $array)
    {
        return implode("\n" . $this->formatter->getHr() . "\n", $array);
    }
}
