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

namespace Chevere\Trace;

use Chevere\Message\Message;
use Chevere\Str\StrBool;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Trace\Interfaces\TraceEntryInterface;
use ReflectionMethod;

final class TraceEntry implements TraceEntryInterface
{
    private string $file;

    private int $line;

    private string $fileLine;

    private string $function;

    private string $class;

    private string $type;

    private array $args;

    public function __construct(
        private array $entry
    ) {
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
        if ($missing !== []) {
            throw new InvalidArgumentException(
                (new Message('Missing key(s) %keyNames%'))
                    ->code('%keyNames%', implode(', ', $missing))
            );
        }
    }

    private function processEntry(): void
    {
        $this->line = $this->entry['line'] ?? 0;
        $this->args = $this->entry['args'] ?? [];
        foreach (array_diff(self::KEYS, ['line', 'args']) as $propName) {
            $this->{$propName} = $this->entry[$propName] ?? '';
        }
    }

    private function handleMissingClassFile()
    {
        if ($this->file === '' && $this->class !== '') {
            /** @var class-string $this->class */
            $reflector = new ReflectionMethod($this->class, $this->function);
            $filename = $reflector->getFileName();
            if ($filename) {
                $this->file = $filename;
                $this->line = $reflector->getStartLine();
            }
        }
    }

    private function handleAnonClass()
    {
        if (
            (new StrBool($this->class))
                ->startsWith('class@anonymous')
        ) {
            preg_match('#class@anonymous(.*):(\d+)#', $this->class, $matches);
            $this->class = 'class@anonymous';
            $this->file = $matches[1];
            $this->line = (int) $matches[2];
        }
    }
}
