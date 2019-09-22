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

class Home extends Controller implements RenderContract
{

    public function __invoke()
    { }

    public function getContent(): string
    {
        return 'Hello World!';
    }

    public function render(): void
    {
        echo $this->getContent();
    }
}
