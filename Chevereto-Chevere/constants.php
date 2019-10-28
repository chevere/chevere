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

namespace Chevere;

define('BOOTSTRAP_TIME', (int) hrtime(true));
define('Chevere\DOCUMENT_ROOT', dirname(__DIR__, basename(__DIR__) == 'Chevereto-Chevere' ? 1 : 3));
define('Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', DOCUMENT_ROOT), '/') . '/');
define('Chevere\APP_PATH', ROOT_PATH . 'app/');
