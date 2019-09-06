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

namespace App\Controllers;

use Chevere\Contracts\Render\RenderContract;
use Chevere\Controller\Controller;
use Chevere\JsonApi\Data;

class Home extends Controller implements RenderContract
{
    public function __invoke()
    {
        $api = new Data('hello', 'World!');
        $this->document->appendData($api);
    }

    public function render()
    {
        echo $this->document->toString();
    }
}
