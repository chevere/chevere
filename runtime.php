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

namespace Chevere;

use Chevere\Components\Instances\RequestInstance;
use Chevere\Components\Instances\RuntimeInstance;
use Chevere\Components\Http\Request;
use Chevere\Components\Runtime\Runtime;
use Chevere\Components\Runtime\Sets\SetDebug;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use Chevere\Components\Runtime\Sets\SetLocale;
use Chevere\Components\Runtime\Sets\SetPrecision;
use Chevere\Components\Runtime\Sets\SetTimeZone;
use Chevere\Components\Runtime\Sets\SetUriScheme;

new RequestInstance(
    Request::fromGlobals()
);

new RuntimeInstance(
    new Runtime(
        new SetDebug('1'),
        // new SetErrorHandler('Chevere\Components\ExceptionHandler\ErrorHandler::error'),
        // new SetExceptionHandler('Chevere\Components\ExceptionHandler\ExceptionHandler::exception'),
        new SetLocale('en_US.UTF8'),
        new SetDefaultCharset('UTF-8'),
        new SetPrecision('16'),
        new SetUriScheme('https'),
        new SetTimeZone('UTC')
    )
);
