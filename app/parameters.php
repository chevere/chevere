<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Chevere\App\Parameters;

return [
  Parameters::API => 'src/Api/',
  Parameters::ROUTES => [
    'routes:dashboard',
    'routes:web',
  ],
];
