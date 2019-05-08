<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use ReflectionMethod;

class ErrorHandlerStack
{
    /** @var array */
    protected $rich;

    /** @var array */
    protected $plain;

    /** @var array */
    protected $console;

    public function __construct(array $trace)
    {
        $i = 0;
        foreach ($trace as $frame) {
            $plainArgs = [];
            $richArgs = [];
            $plainArgsString = null;
            $richArgsString = null;
            // Fill empty file+line using reflection
            if (!array_key_exists('file', $frame) && isset($frame['class'])) {
                $reflector = new ReflectionMethod($frame['class'], $frame['function']);
                $filename = $reflector->getFileName();
                if (false !== $filename) {
                    $frame['file'] = $filename;
                    $frame['line'] = $reflector->getStartLine();
                }
            }
            if (isset($frame['args']) && is_array($frame['args'])) {
                foreach ($frame['args'] as $k => $v) {
                    $aux = 'Arg#'.($k + 1).' ';
                    $plainArgs[] = $aux.Utils\DumpPlain::out($v, null, [App::class]);
                    $richArgs[] = $aux.Utils\Dump::out($v, null, [App::class]);
                }
                if ($plainArgs) {
                    $plainArgsString = "\n".implode("\n", $plainArgs);
                    $richArgsString = "\n".implode("\n", $richArgs);
                }
            }
            if (isset($frame['class']) && Utils\Str::startsWith(Utils\Dump::ANON_CLASS, $frame['class'])) {
                $frameFile = Utils\Str::replaceFirst(Utils\Dump::ANON_CLASS, null, $frame['class']);
                $frame['file'] = substr($frameFile, 0, strpos($frameFile, '.php') + 4);
                $frame['class'] = Utils\Dump::ANON_CLASS;
                $frame['line'] = null;
            }
            if ($frame['function'] == Core::namespaced('autoloader')) {
                $frame['file'] = $frame['file'] ?? (PATH.'autoloader.php');
            }
            if (isset($frame['file']) && Utils\Str::contains('\\', $frame['file'])) {
                $frame['file'] = Path::normalize($frame['file']);
            }
            $plainTable = [
              '%x%' => ($i & 1) ? 'pre--even' : null,
              '%i%' => $i,
              '%f%' => $frame['file'] ?? null,
              '%l%' => $frame['line'] ?? null,
              '%fl%' => isset($frame['file']) ? ($frame['file'].':'.$frame['line']) : null,
              '%c%' => $frame['class'] ?? null,
              '%t%' => $frame['type'] ?? null,
              '%m%' => $frame['function'],
              '%a%' => $plainArgsString,
          ];
            $richTable = $plainTable;
            array_pop($richTable);
            // Dump types map
            foreach ([
                  '%f%' => Utils\Dump::_FILE,
                  '%l%' => Utils\Dump::_FILE,
                  '%fl%' => Utils\Dump::_FILE,
                  '%c%' => Utils\Dump::_CLASS,
                  '%t%' => Utils\Dump::_OPERATOR,
                  '%m%' => Utils\Dump::_FUNCTION,
              ] as $k => $v) {
                $richTable[$k] = isset($plainTable[$k]) ? Utils\Dump::wrap($v, $plainTable[$k]) : null;
            }
            $richTable['%a%'] = $richArgsString;
            if (php_sapi_name() == 'cli') {
                $this->console[] = strtr(ErrorHandler::CONSOLE_STACK_TEMPLATE, $richTable);
            }
            $this->plain[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $plainTable);
            $this->rich[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $richTable);
            ++$i;
        }
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
