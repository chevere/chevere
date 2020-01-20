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

use DateTime;
use DateTimeZone;
use Throwable;
use Chevere\Components\App\Instances\RuntimeInstance;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler
{
    private int $timestamp;

    private string $dateTimeAtom;

    private string $id;

    private RuntimeInstance $runtimeInstance;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(Throwable $exception)
    {
        $this->setTimeProperties();
        $this->id = uniqid('', true);
    }

    public function withRuntimeInstance(RuntimeInstance $runtimeInstance)
    {
        $new = clone $this;
        $new->runtimeInstance = $runtimeInstance;

        return $new;
    }

    private function setTimeProperties(): void
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        $this->dateTimeAtom = $dt->format(DateTime::ATOM);
        $this->timestamp = $dt->getTimestamp();
    }
}
