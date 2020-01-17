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

namespace App\Controllers;

use Chevere\Components\Controller\Interfaces\StringInterface;
use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\Traits\ResponseStringTrait;

class Look extends Controller implements StringInterface
{
    use ResponseStringTrait;

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
