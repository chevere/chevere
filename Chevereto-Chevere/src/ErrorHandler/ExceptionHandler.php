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

use Throwable;
use const Chevereto\Chevere\CORE_NS_HANDLE;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Utils\Str;

/**
 * Handles the error exception throwable.
 */
class ExceptionHandler
{
    /** @var Throwable */
    public $exception;

    /** @var ErrorHandler */
    public $errorHandler;

    /** @var string */
    public $className;

    /** @var string */
    public $code;

    /** @var string */
    public $errorType;

    /** @var string */
    public $type;

    /** @var string */
    public $loggerLevel;

    /** @var string */
    public $message;

    /** @var string */
    public $file;

    /** @var int */
    public $line;

    /** @var Throwable $exception */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
        $this->className = get_class($exception);
        if (Str::startsWith(CORE_NS_HANDLE, $this->className)) {
            $this->className = Str::replaceFirst(CORE_NS_HANDLE, null, $this->className);
        }
        if ($exception instanceof ErrorException) {
            $this->code = $exception->getSeverity();
            $this->errorType = $this->code;
        } else {
            $this->code = $exception->getCode();
            $this->errorType = E_ERROR;
        }
        $this->type = ErrorHandler::getErrorByCode($this->errorType);
        $this->loggerLevel = ErrorHandler::getLoggerLevel($this->errorType) ?? 'error';
        $this->message = $exception->getMessage();
        $this->file = Path::normalize($exception->getFile());
        $this->line = (int) $exception->getLine();
    }
}
