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

namespace Chevere\HttpController;

class HttpRedirectStaticController extends HttpRedirectController
{
    /**
     * @return array<string, mixed>
     * @codeCoverageIgnore
     */
    public function run(): array
    {
        return $this->data();
    }
}
