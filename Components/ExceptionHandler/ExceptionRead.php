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

use Chevere\Components\Exception\DomainException;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionReadInterface;
use Chevere\Components\Message\Interfaces\MessageInterface;
use Chevere\Components\Message\Message;
use ErrorException;
use Exception;
use LogicException;

/**
 * Class used to make Exception readable (normalized).
 */
final class ExceptionRead implements ExceptionReadInterface
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
    public function __construct(Exception $exception)
    {
        $this->className = get_class($exception);
        $this->code = $exception->getCode();
        if ($exception instanceof ErrorException) {
            $this->severity = $exception->getSeverity();
            if (0 == $this->code) {
                $this->code = $this->severity;
            }
        } else {
            $this->severity = ExceptionReadInterface::DEFAULT_ERROR_TYPE;
        }
        $this->assertSeverity();
        $this->loggerLevel = ExceptionReadInterface::ERROR_LEVELS[$this->severity];
        $this->type = ExceptionReadInterface::ERROR_TYPES[$this->severity];
        if ($exception instanceof ExceptionInterface) {
            $this->message = $exception->message();
        } else {
            $this->message = new Message($exception->getMessage());
        }
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();
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
        $accepted = array_keys(ExceptionReadInterface::ERROR_TYPES);
        if (!array_key_exists($this->severity, $accepted)) {
            throw new DomainException(
                (new Message('Unknown severity value of %severity%, accepted values are: %accepted%'))
                    ->code('%severity%', (string) $this->severity)
                    ->code('%accepted%', implode(', ', $accepted))
            );
        }
    }
}
