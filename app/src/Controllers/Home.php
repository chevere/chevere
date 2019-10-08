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

use Chevere\Contracts\Controller\StringContract;
use Chevere\Controller\Controller;
use Chevere\Controller\Traits\ResponseStringTrait;

class Home extends Controller implements StringContract
{
    use ResponseStringTrait;

    /** @var string */
    private $document;

    public function __invoke(): void
    {
        $this->document = 'Hello World!';
    }

    public function getDocument(): string
    {
        return $this->document;
    }
}
