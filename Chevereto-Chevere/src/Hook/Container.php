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

namespace Chevere\Hook;

/**
 * A container for the registered hooks.
 */
final class Container
{
    /** @var array */
    private $array;

    public function __construct()
    {
        $this->array = [
            'App\Controllers\Home' => [
                'helloWorld' => [
                    10 => [
                        [
                            'callable' => 'Plugins\Local\HelloWorld\Hooks\Controllers\Home\HelloWorld',
                            'maker' => 'somefile.php'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getAnchor(object $that, string $anchor): array
    {
        return $this->array[get_class($that)][$anchor] ?? [];
    }
}
