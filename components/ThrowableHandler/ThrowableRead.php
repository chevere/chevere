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
use Chevere\Exceptions\Core\DomainException;
use Chevere\Exceptions\Core\Exception as CoreException;
use Chevere\Interfaces\Message\MessageInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableReadInterface;
use ErrorException;
use LogicException;
use Throwable;

/**
 * Class used to make Exception readable (normalized).
 */
final class ThrowableRead implements ThrowableReadInterface
{
    private string $className;

    private int $code;

    private int $severity;

    private string $loggerLevel;

    private string $type;

    private MessageInterface $message;

    private string $file;

    private int $line;

    private array $trace;

    /**
     * @throws LogicException if the exception severity is unknown.
     */
    public function __construct(Throwable $throwable)
    {
        $this->className = get_class($throwable);
        $this->code = $throwable->getCode();
        if ($throwable instanceof ErrorException) {
            $this->severity = $throwable->getSeverity();
            if ($this->code === 0) {
                $this->code = $this->severity;
            }
        } else {
            $this->severity = ThrowableReadInterface::DEFAULT_ERROR_TYPE;
        }
        $this->assertSeverity();
        $this->loggerLevel = ThrowableReadInterface::ERROR_LEVELS[$this->severity];
        $this->type = ThrowableReadInterface::ERROR_TYPES[$this->severity];
        if ($throwable instanceof CoreException) {
            $this->message = $throwable->message();
        } else {
            $this->message = new Message($throwable->getMessage());
        }
        $this->file = $throwable->getFile();
        $this->line = $throwable->getLine();
        $this->trace = $throwable->getTrace();
    }

    public function className(): string
    {
        return $this->className;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function severity(): int
    {
        return $this->severity;
    }

    public function loggerLevel(): string
    {
        return $this->loggerLevel;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function message(): MessageInterface
    {
        return $this->message;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function trace(): array
    {
        return $this->trace;
    }

    private function assertSeverity(): void
    {
        $accepted = array_keys(ThrowableReadInterface::ERROR_TYPES);
        if (!in_array($this->severity, $accepted)) {
            // @codeCoverageIgnoreStart
            throw new DomainException(
                (new Message('Unknown severity value of %severity%, accepted values are: %accepted%'))
                    ->code('%severity%', (string) $this->severity)
                    ->code('%accepted%', implode(', ', $accepted))
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
