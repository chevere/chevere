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

namespace Chevere\ExceptionHandler\src;

use Throwable;
use ErrorException;
use Chevere\Data\Data;
use Chevere\Path\Path;
use Chevere\Utility\Str;
use Chevere\Contracts\DataContract;
use Chevere\Data\Traits\DataAccessTrait;
use Chevere\Data\Traits\DataKeyTrait;
use Chevere\ExceptionHandler\ExceptionHandler;

/**
 * Wraps throwable exception.
 */
final class Wrap
{
    use DataAccessTrait;
    use DataKeyTrait;

    /** @var Throwable */
    private $exception;

    /** @var DataContract */
    private $data;

    /** @var Throwable $exception */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
        $this->data = new Data();
        $className = get_class($exception);
        if (Str::startsWith('Chevere\\', $className)) {
            $className = Str::replaceFirst('Chevere\\', '', $className);
        }
        if ($exception instanceof ErrorException) {
            /* @scrutinizer ignore-call */
            $phpCode = $exception->getSeverity();
            $code = $phpCode;
            $errorType = $phpCode;
        } else {
            $phpCode = E_ERROR;
            $code = $exception->getCode();
            $errorType = $phpCode;
        }
        $this->data->add([
            'className' => $className,
            'code' => $code,
            'errorType' => $errorType,
            'type' => ExceptionHandler::ERROR_TABLE[$phpCode],
            'loggerLevel' => ExceptionHandler::PHP_LOG_LEVEL[$phpCode] ?? 'error',
            'message' => $exception->getMessage(),
            'file' => Path::normalize($exception->getFile()),
            'line' => (int) $exception->getLine(),
        ]);
    }

    public function exception(): Throwable
    {
        return $this->exception;
    }
}
