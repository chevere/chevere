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

use InvalidArgumentException;
use ReflectionMethod;
use Chevere\Components\ExceptionHandler\Interfaces\TraceEntryInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

/**
 * Allows to interact with trace entries thrown by Exceptions.
 */
final class TraceEntry implements TraceEntryInterface
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
     * Creates a new instance.
     *
     * @param array $entry An exception trace item.
     * @throws InvalidArgumentException If $entry doesn't contain the required TraceEntryInterface::KEYS.
     */
    public function __construct(array $entry)
    {
        $this->entry = $entry;
        $this->assertEntry();
        $this->processEntry();
        if ('' == $this->file && '' != $this->class) {
            $this->processMissingClassFile();
        }
        if (stringStartsWith(VarDumpInterface::_CLASS_ANON, $this->class)) {
            $this->processAnonClass();
        }
        if ('' == $this->file) {
            $this->fileLine = '';
            $this->line = 0;
        } else {
            $this->fileLine = $this->file . ':' . $this->line;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function line(): int
    {
        return $this->line;
    }

    /**
     * {@inheritdoc}
     */
    public function fileLine(): string
    {
        return $this->fileLine;
    }

    /**
     * {@inheritdoc}
     */
    public function function(): string
    {
        return $this->function;
    }

    /**
     * {@inheritdoc}
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function args(): array
    {
        return $this->args;
    }

    private function assertEntry(): void
    {
        $missing = [];
        foreach (static::MUST_HAVE_KEYS as $key) {
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
        foreach ([
            'file',
            'function',
            'class',
            'type',
        ] as $propName) {
            $this->$propName = $this->entry[$propName] ?? '';
        }
        $this->line = $this->entry['line'] ?? 0;
        $this->args = $this->entry['args'] ?? [];
    }

    private function processMissingClassFile()
    {
        $reflector = new ReflectionMethod($this->class, $this->function);
        $filename = $reflector->getFileName();
        if (false !== $filename) {
            $this->file = $filename;
            $this->line = $reflector->getStartLine();
        }
    }

    private function processAnonClass()
    {
        $entryFile = stringReplaceFirst(VarDumpInterface::_CLASS_ANON, '', $this->class);
        $this->file = substr($entryFile, 0, 4 + strpos($entryFile, '.php'));
        $this->class = VarDumpInterface::_CLASS_ANON;
        $this->line = 0;
    }
}
