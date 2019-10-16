<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controllers;

use Chevere\Contracts\Controller\StringContract;
use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\Traits\ResponseStringTrait;

class Home extends Controller implements StringContract
{
    use ResponseStringTrait;

    /** @var string */
    private $document;

    public function __invoke(): void
    {
        $this->document = 'Hello World!'; // Sets Hello World!
        $this->hook('helloWorld'); // Hooks run (if any)...
        $this->document .= ' >> After hook'; // Hooks + zz
        // die(1000 * (microtime(true) - BOOTSTRAP_TIME));
    }

    public function setDocument(string $document): void
    {
        $this->document = $document;
    }

    public function getDocument(): string
    {
        return $this->document;
    }
}
