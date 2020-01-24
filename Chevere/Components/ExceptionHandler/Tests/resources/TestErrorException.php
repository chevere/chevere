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

namespace Chevere\Components\ExceptionHandler\Tests\resources;

/**
 * A dummy ErrorException that allows to inject severity values.
 */
final class TestErrorException extends \ErrorException
{
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }
}
