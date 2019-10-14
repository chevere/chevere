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

final class Hooks
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

    public function exec(string $anchor, object $that): void
    {
        $hooks = $this->getAnchor($that, $anchor);
        if (null == $hooks) {
            return;
        }
        $this->runner($hooks, $that);
    }

    private function getAnchor(object $that, string $anchor): ?array
    {
        return $this->array[get_class($that)][$anchor] ?? null;
    }

    private function runner(array $hooks, object $that): void
    {
        foreach ($hooks as $entries) {
            foreach ($entries as $entry) {
                $hook = new $entry['callable'];
                $hook($that);
            }
        }
    }
}
