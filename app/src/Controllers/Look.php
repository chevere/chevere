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
use Chevere\Controller\Traits\StringTrait;

class Look extends Controller implements StringContract
{
    use StringTrait;

    /** @var array */
    private $keys;

    public function __invoke(): void
    {
        $this->keys = [
            'prop1' => 'val1',
            'prop2' => 'val2',
            'prop3' => 'val3',
            'prop4' => 'val4',
        ];
    }

    public function getDocument(): string
    {
        return implode('--', $this->keys);
    }
}
