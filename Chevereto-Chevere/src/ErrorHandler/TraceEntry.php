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

namespace Chevereto\Chevere\ErrorHandler;

use ReflectionMethod;
use Chevereto\Chevere\Core;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Utils\Str;
use Chevereto\Chevere\Utils\Dump;
use Chevereto\Chevere\Utils\DumpPlain;

/**
 * TraceEntry prepares the Exception trace for being used with our Stack.
 */
class TraceEntry
{
    /** @var array Exception trace entry */
    protected $entry;

    /** @var array Input Exception trace entry provided */
    protected $_entry;

    /** @var string Plain representation of the entry arguments */
    protected $plainArgs;

    /** @var string Rich representation of the entry arguments (colored) */
    protected $richArgs;

    public function getArray(): array
    {
        return $this->entry;
    }

    public function __construct(array $entry)
    {
        $this->_entry = $entry;
        $this->entry = $entry;
        $this->handleProcessMissingClassFile();
        $this->handleSetEntryArguments();
        $this->handleProcessAnonClass();
        $this->handleProcessCoreAutoloader();
        $this->handleProcessNormalizeFile();
    }

    public function getPlainArgs(): ?string
    {
        return $this->plainArgs;
    }

    public function getRichArgs(): ?string
    {
        return $this->richArgs;
    }

    protected function handleProcessMissingClassFile()
    {
        if (!array_key_exists('file', $this->entry) && isset($this->entry['class'])) {
            return $this->processMissingClassFile();
        }
    }

    protected function processMissingClassFile()
    {
        $reflector = new ReflectionMethod($this->entry['class'], $this->entry['function']);
        $filename = $reflector->getFileName();
        if (false !== $filename) {
            $this->entry['file'] = $filename;
            $this->entry['line'] = $reflector->getStartLine();
        }
    }

    protected function handleSetEntryArguments()
    {
        if (isset($this->entry['args']) && is_array($this->entry['args'])) {
            $this->setFrameArguments($this->entry);
        }
    }

    protected function setFrameArguments()
    {
        foreach ($this->entry['args'] as $k => $v) {
            $aux = 'Arg#' . ($k + 1) . ' ';
            $plainArgs[] = $aux . DumpPlain::out($v, null, [App::class]);
            $richArgs[] = $aux . Dump::out($v, null, [App::class]);
        }
        if (isset($plainArgs)) {
            $this->plainArgs = "\n" . implode("\n", $plainArgs);
            $this->richArgs = "\n" . implode("\n", $richArgs);
        }
    }

    protected function handleProcessAnonClass()
    {
        if (isset($this->entry['class']) && Str::startsWith(Dump::ANON_CLASS, $this->entry['class'])) {
            $this->processAnonClass();
        }
    }

    protected function processAnonClass()
    {
        $entryFile = Str::replaceFirst(Dump::ANON_CLASS, null, $this->entry['class']);
        $this->entry['file'] = substr($entryFile, 0, strpos($entryFile, '.php') + 4);
        $this->entry['class'] = Dump::ANON_CLASS;
        $this->entry['line'] = null;
    }

    protected function handleProcessCoreAutoloader()
    {
        if ($this->entry['function'] == Core::namespaced('autoloader')) {
            $this->processCoreAutoloader();
        }
    }

    protected function processCoreAutoloader()
    {
        $this->entry['file'] = $this->entry['file'] ?? (PATH . 'autoloader.php');
    }

    protected function handleProcessNormalizeFile()
    {
        if (isset($this->entry['file']) && Str::contains('\\', $this->entry['file'])) {
            $this->processNormalizeFile();
        }
    }

    protected function processNormalizeFile()
    {
        $this->entry['file'] = Path::normalize($this->entry['file']);
    }
}
