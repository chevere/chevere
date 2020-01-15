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

namespace Chevere\Components\ExceptionHandler\Tests;

use Chevere\Components\ExceptionHandler\ExceptionHandler;
use LogicException;
use PHPUnit\Framework\TestCase;
use TypeError;

final class ExceptionHandlerTest extends TestCase
{
    public function testConstruct(): void
    {
        // set_exception_handler('Chevere\Components\ExceptionHandler\ExceptionHandler::exception');
        // throw new LogicException('yeaaaaaah', 1313);
        // die();
        // new ExceptionHandler(1, 'Test Exception', __FILE__, __LINE__, new TypeError('upsi'));
    }
}
