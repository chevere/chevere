<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

return
  (new AppOptions())
      ->addConfigFile(':config')
      ->addApi('api', 'apis/api')
      ->addApi('api-alt', 'apis/api-alt')
      ->addRoute('routes:dashboard')
      ->addRoute('routes:web');
