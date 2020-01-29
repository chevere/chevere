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
/*
 * Checks if this server meets minimum PHP requirement
 * I do this in a separated file so I don't have to mix new+old syntax on my code
 */

namespace Chevere;

const MIN_PHP_VERSION = '7.3.0';
if (version_compare(PHP_VERSION, MIN_PHP_VERSION, '<')) {
    http_response_code(500);
    trigger_error('This is running PHP ' . PHP_VERSION . ' and ' . __NAMESPACE__ . ' requires at least PHP ' . MIN_PHP_VERSION, E_USER_ERROR);
}
