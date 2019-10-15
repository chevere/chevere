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

require dirname(__DIR__, 1) . '/vendor/autoload.php';
define('Chevere\ROOT_PATH', dirname(__DIR__, 1) . '/');
define('Chevere\PATH', __DIR__ . '/');
define('Chevere\App\PATH', 'app/');
define('Chevere\CLI', true);
