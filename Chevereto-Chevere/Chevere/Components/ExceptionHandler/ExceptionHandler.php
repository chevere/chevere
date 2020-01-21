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

use BadMethodCallException;
use DateTime;
use DateTimeZone;
use DateTimeInterface;
use Chevere\Components\App\Instances\RuntimeInstance;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Interfaces\RuntimeInterface;
use Monolog\Logger;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler implements ExceptionHandlerInterface
{
    private DateTimeInterface $dateTimeUtc;

    private ExceptionInterface $exception;

    private string $id;

    private RuntimeInterface $runtime;

    private RequestInterface $request;

    private Logger $logger;

    private bool $isDebug = false;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(\Exception $exception)
    {
        $this->dateTimeUtc = new DateTime('now', new DateTimeZone('UTC'));
        $this->exception = new Exception($exception);
        $this->id = uniqid('', true);
    }

    public static function function($exception): void
    {
        new static($exception);
    }

    public function dateTimeUtc(): DateTimeInterface
    {
        return $this->dateTimeUtc;
    }

    public function exception(): ExceptionInterface
    {
        return $this->exception;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->isDebug = $isDebug;

        return $new;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    /**
     * {@inheritdoc}
     */
    public function withRuntime(RuntimeInterface $runtime): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->runtime = $runtime;
        $new->isDebug = (bool) $runtime->data()->key('debug');

        return $new;
    }

    public function hasRuntime(): bool
    {
        return isset($this->runtime);
    }

    /**
     * {@inheritdoc}
     */
    public function runtime(): RuntimeInterface
    {
        $this->assertPropertyMethod();

        return $this->runtime;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(RequestInterface $request): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    /**
     * {@inheritdoc}
     */
    public function request(): RequestInterface
    {
        $this->assertPropertyMethod();

        return $this->request;
    }

    public function withLogger(Logger $logger): ExceptionHandlerInterface
    {
        $new = clone $this;
        $new->logger = $logger;

        return $new;
    }

    public function hasLogger(): bool
    {
        return isset($this->logger);
    }

    public function logger(): Logger
    {
        $this->assertPropertyMethod();

        return $this->logger;
    }

    private function assertPropertyMethod(): void
    {
        $propertyName = debug_backtrace(0, 2)[1]['function'];
        if (!isset($this->$propertyName)) {
            throw new BadMethodCallException(
                (new Message('The method %method% must be called only if the property %propertyName% exists in the instance'))
                    ->code('%method%', __CLASS__ . '::' . $propertyName)
                    ->code('%propertyName%', $propertyName)
                    ->toString()
            );
        }
    }

    // private function setTimeProperties(): void
    // {
    //     $dt = new DateTime('now', new DateTimeZone('UTC'));
    //     $this->dateTimeAtom = $dt->format(DateTime::ATOM);
    //     $this->timestamp = $dt->getTimestamp();
    // }
}
