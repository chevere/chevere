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

namespace Chevere\Components\ThrowableHandler;

use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrBool;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceEntryInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use InvalidArgumentException;
use ReflectionMethod;

/**
 * Allows to interact with trace entries thrown by Exception.
 */
final class ThrowableTraceEntry implements ThrowableTraceEntryInterface
{
    private array $entry;

    private string $file;

    private int $line;

    private string $fileLine;

    private string $function;

    private string $class;

    private string $type;

    private array $args;

    /**
     * @param array $entry An exception trace item.
     * @throws InvalidArgumentException If $entry doesn't contain the required TraceEntryInterface::KEYS.
     */
    public function __construct(array $entry)
    {
        $this->entry = $entry;
        $this->assertEntry();
        $this->processEntry();
        $this->handleAnonClass();
        $this->handleMissingClassFile();
        if ($this->file === '') {
            $this->fileLine = '';
            $this->line = 0;
        } else {
            $this->fileLine = $this->file . ':' . $this->line;
        }
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function fileLine(): string
    {
        return $this->fileLine;
    }

    public function function(): string
    {
        return $this->function;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function args(): array
    {
        return $this->args;
    }

    private function assertEntry(): void
    {
        $missing = [];
        foreach (self::MUST_HAVE_KEYS as $key) {
            if (!array_key_exists($key, $this->entry)) {
                $missing[] = $key;
            }
        }
        if (!empty($missing)) {
            throw new InvalidArgumentException(
                (new Message('Missing key(s) %keyNames%'))
                    ->code('%keyNames%', implode(', ', $missing))
                    ->toString()
            );
        }
    }

    private function processEntry(): void
    {
        $this->line = $this->entry['line'] ?? 0;
        $this->args = $this->entry['args'] ?? [];
        foreach (self::KEYS as $propName) {
            if (in_array($propName, ['line', 'args'])) {
                continue;
            }
            $this->$propName = $this->entry[$propName] ?? '';
        }
    }

    private function handleMissingClassFile()
    {
        if ($this->file === '' && $this->class !== '') {
            $reflector = new ReflectionMethod($this->class, $this->function);
            $filename = $reflector->getFileName();
            if (false !== $filename) {
                $this->file = $filename;
                $this->line = $reflector->getStartLine();
            }
        }
    }

    private function handleAnonClass()
    {
        if ((new StrBool($this->class))->startsWith(VarDumperInterface::CLASS_ANON) === true) {
            preg_match('#^class@anonymous(.*):(\d+)#', $this->class, $matches);
            $this->class = VarDumperInterface::CLASS_ANON;
            $this->file = $matches[1];
            $this->line = (int) $matches[2];
        }
    }
}
