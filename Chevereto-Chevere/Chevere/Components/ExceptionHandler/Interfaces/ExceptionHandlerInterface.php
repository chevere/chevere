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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Runtime\Interfaces\RuntimeInterface;
use DateTimeInterface;
use Monolog\Logger;

interface ExceptionHandlerInterface
{
    public function __construct(\Exception $exception);

    public function dateTimeUtc(): DateTimeInterface;

    public function exception(): ExceptionInterface;

    public function id(): string;

    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface;

    public function isDebug(): bool;

    public function withRuntime(RuntimeInterface $runtime): ExceptionHandlerInterface;

    public function hasRuntime(): bool;

    public function runtime(): RuntimeInterface;

    public function withRequest(RequestInterface $request): ExceptionHandlerInterface;

    public function hasRequest(): bool;

    public function request(): RequestInterface;

    public function withLogger(Logger $logger): ExceptionHandlerInterface;

    public function hasLogger(): bool;

    public function logger(): Logger;
}
