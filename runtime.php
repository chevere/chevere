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

use Chevere\Components\Instances\RuntimeInstance;
use Chevere\Components\Runtime\Runtime;
use Chevere\Components\Runtime\Sets\SetDebug;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use Chevere\Components\Runtime\Sets\SetLocale;
use Chevere\Components\Runtime\Sets\SetPrecision;
use Chevere\Components\Runtime\Sets\SetTimeZone;
use Chevere\Components\Runtime\Sets\SetUriScheme;

new RuntimeInstance(
    (new Runtime)
        ->withSet(new SetDebug('1'))
        ->withSet(new SetLocale('en_US.UTF8'))
        ->withSet(new SetDefaultCharset('UTF-8'))
        ->withSet(new SetPrecision('16'))
        ->withSet(new SetUriScheme('https'))
        ->withSet(new SetTimeZone('UTC'))
        //->withSet(new SetErrorHandler('Chevere\Components\ThrowableHandler\ErrorHandler::error')),
        //->withSet(new SetExceptionHandler('Chevere\Components\ThrowableHandler\ExceptionHandler::exception')),
);
