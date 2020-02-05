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

use ErrorException;
use LogicException;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\Message\Message;

/**
 * Class used to make Exceptions readable (normalized).
 */
final class Exception implements ExceptionInterface
{
    private string $className;

    private int $code;

    private int $severity;

    private string $loggerLevel;

    private string $type;

    private string $message;

    private string $file;

    private int $line;

    private array $trace;

    /**
     * Creates a new instance.
     *
     * @throws LogicException if the exception severity is unknown.
     */
    public function __construct(\Exception $exception)
    {
        $this->className = get_class($exception);
        $this->code = $exception->getCode();
        if ($exception instanceof ErrorException) {
            $this->severity = $exception->getSeverity();
            if (0 == $this->code) {
                $this->code = $this->severity;
            }
        } else {
            $this->severity = ExceptionInterface::DEFAULT_ERROR_TYPE;
        }
        $this->assertSeverity();
        $this->loggerLevel = ExceptionInterface::ERROR_LEVELS[$this->severity];
        $this->type = ExceptionInterface::ERROR_TYPES[$this->severity];
        $this->message = $exception->getMessage();
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

    public function message(): string
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
        foreach ([ExceptionInterface::ERROR_TYPES, ExceptionInterface::ERROR_LEVELS] as $array) {
            if (!array_key_exists($this->severity, $array)) {
                $accepted = array_keys($array);
                throw new LogicException(
                    (new Message('Unknown severity value of %severity%, accepted values are: %accepted%'))
                        ->code('%severity%', (string) $this->severity)
                        ->code('%accepted%', implode(', ', $accepted))
                        ->toString()
                );
            }
        }
    }
}
