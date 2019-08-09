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

namespace Chevere\Contracts\Api;

use Chevere\PathHandle;
use Chevere\Contracts\Router\RouterContract;

interface MakerContract
{
    public function __construct(RouterContract $router);

    /**
     * Automatically finds controllers in the given path and generate the API route binding.
     *
     * @param string $pathIdentifier path identifier representing the dir containing API controllers (src/Api/)
     */
    public function register(PathHandle $pathHandle): void;

    public function api(): array;
}
