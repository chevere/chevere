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

namespace Chevere\Controller;

use function Chevere\DataStructure\data;

final class HttpRedirectStaticController extends HttpRedirectController
{
    /**
     * @return array<string, mixed>
     * @codeCoverageIgnore
     */
    public function run(): array
    {
        return data(
            uri: $this->uri(),
            status: $this->status(),
        );
    }
}
