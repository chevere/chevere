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

namespace Chevere\ThrowableHandler;

use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerInterface;
use Chevere\ThrowableHandler\Interfaces\ThrowableReadInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Throwable;

final class ThrowableHandler implements ThrowableHandlerInterface
{
    public const ERRORS_AS_EXCEPTIONS = __NAMESPACE__ . '\errorsAsExceptions';

    public const CONSOLE_HANDLER = __NAMESPACE__ . '\consoleHandler';
    
    public const HTML_HANDLER =
    __NAMESPACE__ . '\htmlHandler';
    
    public const PLAIN_HANDLER = __NAMESPACE__ . '\plainHandler';

    public const FATAL_ERROR_HANDLER = __NAMESPACE__ . '\fatalErrorHandler';
    
    private DateTimeInterface $dateTimeUtc;

    private string $id;

    private bool $isDebug = true;

    public function __construct(
        private ThrowableReadInterface $throwableRead
    ) {
        try {
            $this->dateTimeUtc = new DateTimeImmutable(
                'now',
                new DateTimeZone('UTC')
            );
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to create %var%: %error%'))
                    ->code('%var%', 'dateTimeUtc')
                    ->strtr('%error%', $e->getMessage())
            );
        }
        // @codeCoverageIgnoreEnd
        $this->id = uniqid('');
    }

    public function withIsDebug(bool $isDebug): ThrowableHandlerInterface
    {
        $new = clone $this;
        $new->isDebug = $isDebug;

        return $new;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    public function dateTimeUtc(): DateTimeInterface
    {
        return $this->dateTimeUtc;
    }

    public function throwableRead(): ThrowableReadInterface
    {
        return $this->throwableRead;
    }

    public function id(): string
    {
        return $this->id;
    }
}
