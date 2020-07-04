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

namespace Chevere\Tests\ThrowableHandler;

use Chevere\Components\ThrowableHandler\ThrowableHandlerHandle;
use ErrorException;
use PHPUnit\Framework\TestCase;

final class ThrowableHandlerHandleTest extends TestCase
{
    public function testError(): void
    {
        $this->expectException(ErrorException::class);
        ThrowableHandlerHandle::errorsAsExceptions(1, 'Error', __FILE__, __LINE__);
    }
}
