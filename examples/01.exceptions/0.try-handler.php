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

use Chevere\Components\ExceptionHandler\Documents\PlainDocument;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\ExceptionRead;

require 'vendor/autoload.php';

try {
    throw new Exception('Whoops...');
} catch (Exception $e) {
    $handler = new ExceptionHandler(new ExceptionRead($e));
    $document = new PlainDocument($handler);
    echo $document->toString() . "\n";

    die(255);
}
