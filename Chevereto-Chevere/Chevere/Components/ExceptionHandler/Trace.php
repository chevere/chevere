<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
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
use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;

final class Trace implements TraceInterface
{
    private array $trace;

    private FormatterInterface $formatter;

    private VarDumpFormatterInterface $varDumpFormatter;

    private array $array = [];

    private string $string = '';

    /**
     * Creates a new instance.
     */
    public function __construct(array $trace, FormatterInterface $formatter)
    {
        $this->trace = $trace;
        $this->formatter = $formatter;
        $this->varDumpFormatter = $formatter->getVarDumpFormatter();
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
     */
    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->string;
    }

    private function getTrTable(int $pos, TraceEntryInterface $entry): array
    {
        $trValues = [
            '%cssEvenClass%' => ($pos & 1) ? 'pre--even' : '',
            '%i%' => $pos,
            '%file%' => $entry->file(),
            '%line%' => $entry->line(),
            '%fileLine%' => $entry->fileLine(),
            '%class%' => $entry->class(),
            '%type%' => $entry->type(),
            '%function%' => $entry->function(),
        ];
        $array = $trValues;
        foreach (static::HIGHLIGHT_TAGS as $tag => $key) {
            $val = $trValues[$tag];
            if (empty($val)) {
                continue;
            }
            $array[$tag] = $this->varDumpFormatter->applyWrap($key, (string) $trValues[$tag]);
        }
        $array['%arguments%'] = $this->getEntryArguments($entry);

        return $array;
    }

    private function getEntryArguments(TraceEntryInterface $entry): string
    {
        $string = '';
        foreach ($entry->args() as $pos => $var) {
            $string .= "\n";
            $aux = 'Arg#' . ($pos + 1) . ' ';
            $varDump = (new VarDump(new Dumpeable($var), $this->varDumpFormatter))
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
