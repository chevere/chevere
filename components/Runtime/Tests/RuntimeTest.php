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

namespace Chevere\Components\Runtime\Tests;

use Chevere\Components\Runtime\Runtime;
use Chevere\Components\Runtime\Sets\SetDebug;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use Chevere\Components\Runtime\Sets\SetLocale;
use Chevere\Components\Runtime\Sets\SetPrecision;
use Chevere\Components\Runtime\Sets\SetTimeZone;
use Chevere\Components\Runtime\Sets\SetUriScheme;
use PHPUnit\Framework\TestCase;

final class RuntimeTest extends TestCase
{
    public function testConstruct(): void
    {
        $sets = [
            new SetDebug('1'),
            //FIXME:
            // new SetErrorHandler('Chevere\Components\ExceptionHandler\ErrorHandler::error'),
            // new SetExceptionHandler('Chevere\Components\ExceptionHandler\ExceptionHandler::function'),
            new SetLocale('en_US.UTF8'),
            new SetDefaultCharset('UTF-8'),
            new SetPrecision('16'),
            new SetUriScheme('https'),
            new SetTimeZone('UTC')
        ];
        $data = [];
        foreach ($sets as $set) {
            $data[$set->name()] = $set->value();
        }
        $runtime = new Runtime(...$sets);
        $this->assertSame($data, $runtime->data()->toArray());
    }
}
