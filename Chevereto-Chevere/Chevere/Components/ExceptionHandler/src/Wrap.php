<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ExceptionHandler\src;

use ErrorException;
use Throwable;

use Chevere\Components\Data\Data;
use Chevere\Components\Data\Traits\DataMethodTrait;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Contracts\Data\DataContract;

use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

/**
 * Wraps throwable exception.
 */
final class Wrap
{
    use DataMethodTrait;

    /** @var Throwable */
    private $exception;

    /** @var DataContract */
    private $data;

    /** @var Throwable $exception */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
        $this->data = new Data([]);
        $className = get_class($exception);
        if (stringStartsWith('Chevere\\', $className)) {
            $className = stringReplaceFirst('Chevere\\', '', $className);
        }
        $phpCode = E_ERROR;
        $code = $exception->getCode();
        if ($exception instanceof ErrorException) {
            /* @scrutinizer ignore-call */
            $phpCode = $exception->getSeverity();
            $code = $phpCode;
        }
        $errorType = $phpCode;
        $this->data = $this->data
            ->withMergedArray([
                'className' => $className,
                'code' => $code,
                'errorType' => $errorType,
                'type' => ExceptionHandler::ERROR_TABLE[$phpCode],
                'loggerLevel' => ExceptionHandler::PHP_LOG_LEVEL[$phpCode] ?? 'error',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => (int) $exception->getLine(),
            ]);
    }

    public function exception(): Throwable
    {
        return $this->exception;
    }
}
