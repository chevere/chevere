<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere;

use ReflectionMethod;

class ErrorHandlerStack
{
    /** @var array */
    protected $rich;

    /** @var array */
    protected $plain;

    /** @var array */
    protected $console;

    /** @var array The table used to map the rich stack */
    private $richTable;

    /** @var array The table used to map the plain stack */
    private $plainTable;

    /** @var string The argument string line for plain stack */
    private $plainArgsString;

    /** @var string The argument string line for rich stack */
    private $richArgsString;

    public function __construct(array $trace)
    {
        $i = 0;
        foreach ($trace as $frame) {
            $this->assertProcessMissingClassFile($frame);
            $this->assertSetFrameArguments($frame);
            $this->assertProcessAnonClass($frame);
            $this->assertProcessCoreAutoloader($frame);
            $this->assertProcessNormalizeFile($frame);
            $this->setPlainTable($i, $frame, $this->plainArgsString);
            $this->setRichTable($this->plainTable, $this->richArgsString);
            $this->assertProcessConsole($this->richTable);
            $this->processPlain($this->plainTable);
            $this->processRich($this->richTable);
            ++$i;
        }
    }

    protected function assertSetFrameArguments(array $frame)
    {
        if (isset($frame['args']) && is_array($frame['args'])) {
            $this->setFrameArguments($frame);
        }
    }

    protected function setFrameArguments(array $frame)
    {
        foreach ($frame['args'] as $k => $v) {
            $aux = 'Arg#'.($k + 1).' ';
            $plainArgs[] = $aux.Utils\DumpPlain::out($v, null, [App::class]);
            $richArgs[] = $aux.Utils\Dump::out($v, null, [App::class]);
        }
        if (isset($plainArgs)) {
            $this->plainArgsString = "\n".implode("\n", $plainArgs);
            $this->richArgsString = "\n".implode("\n", $richArgs);
        }
    }

    /**
     * Fills empty file+line using reflection.
     */
    protected function assertProcessMissingClassFile(array &$frame)
    {
        if (!array_key_exists('file', $frame) && isset($frame['class'])) {
            return $this->processMissingClassFile($frame);
        }
    }

    protected function processMissingClassFile(array &$frame)
    {
        $reflector = new ReflectionMethod($frame['class'], $frame['function']);
        $filename = $reflector->getFileName();
        if (false !== $filename) {
            $frame['file'] = $filename;
            $frame['line'] = $reflector->getStartLine();
        }
    }

    protected function assertProcessAnonClass(array &$frame)
    {
        if (isset($frame['class']) && Utils\Str::startsWith(Utils\Dump::ANON_CLASS, $frame['class'])) {
            $this->processAnonClass();
        }
    }

    protected function processAnonClass(array &$frame)
    {
        $frameFile = Utils\Str::replaceFirst(Utils\Dump::ANON_CLASS, null, $frame['class']);
        $frame['file'] = substr($frameFile, 0, strpos($frameFile, '.php') + 4);
        $frame['class'] = Utils\Dump::ANON_CLASS;
        $frame['line'] = null;
    }

    protected function assertProcessCoreAutoloader(array &$frame)
    {
        if ($frame['function'] == Core::namespaced('autoloader')) {
            $this->processCoreAutoloader($frame);
        }
    }

    protected function processCoreAutoloader(array &$frame)
    {
        $frame['file'] = $frame['file'] ?? (PATH.'autoloader.php');
    }

    protected function assertProcessNormalizeFile(array &$frame)
    {
        if (isset($frame['file']) && Utils\Str::contains('\\', $frame['file'])) {
            $this->processNormalizeFile($frame);
        }
    }

    protected function processNormalizeFile(array &$frame)
    {
        $frame['file'] = Path::normalize($frame['file']);
    }

    protected function setPlainTable(int $i, array $frame, ?string $args): self
    {
        $this->plainTable = [
            '%x%' => ($i & 1) ? 'pre--even' : null,
            '%i%' => $i,
            '%f%' => $frame['file'] ?? null,
            '%l%' => $frame['line'] ?? null,
            '%fl%' => isset($frame['file']) ? ($frame['file'].':'.$frame['line']) : null,
            '%c%' => $frame['class'] ?? null,
            '%t%' => $frame['type'] ?? null,
            '%m%' => $frame['function'],
            '%a%' => $args,
        ];

        return $this;
    }

    protected function setRichTable(array $plainTable, ?string $args): self
    {
        $this->richTable = $plainTable;
        array_pop($this->richTable);
        // Dump types map
        foreach ([
                '%f%' => Utils\Dump::_FILE,
                '%l%' => Utils\Dump::_FILE,
                '%fl%' => Utils\Dump::_FILE,
                '%c%' => Utils\Dump::_CLASS,
                '%t%' => Utils\Dump::_OPERATOR,
                '%m%' => Utils\Dump::_FUNCTION,
            ] as $k => $v) {
            $this->richTable[$k] = isset($plainTable[$k]) ? Utils\Dump::wrap($v, $plainTable[$k]) : null;
        }
        $this->richTable['%a%'] = $args;

        return $this;
    }

    protected function assertProcessConsole(array $richTable): void
    {
        if (php_sapi_name() == 'cli') {
            $this->processConsole($richTable);
        }
    }

    protected function processConsole(array $richTable): void
    {
        $this->console[] = strtr(ErrorHandler::CONSOLE_STACK_TEMPLATE, $richTable);
    }

    protected function processPlain(array $plainTable): void
    {
        $this->plain[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $plainTable);
    }

    protected function processRich(array $richTable): void
    {
        $this->rich[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $richTable);
    }

    /**
     * Oh, if it were so easy...
     */
    public function getRich(): array
    {
        return $this->rich ?? [];
    }

    public function getPlain(): array
    {
        return $this->plain ?? [];
    }

    public function getConsole(): array
    {
        return $this->console ?? [];
    }
}
