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

// FIXME: App parameters must be passed as simple array for the end-user.
return
  (new AppOptions())
      ->addApi('api', 'apis/api')
      ->addApi('api-alt', 'apis/api-alt')
      ->addRoute('routes:dashboard')
      ->addRoute('routes:web');
