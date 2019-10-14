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

namespace Plugins\Local\HelloWorld\Hooks\Controllers\Home;

use App\Controllers\Home;

/**
 * This class provides an example hook.
 */
class HelloWorld
{
    public function __invoke(Home $that)
    {
        $document = $that->getDocument();
        $that->setDocument($document . ' >> Hello hook!');
    }

    /**
     * Returns the class name where the hook works.
     */
    public function forClass(): string
    {
        return Home::class;
    }
}
