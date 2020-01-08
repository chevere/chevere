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

namespace Chevere\Components\Controller\Contracts;

use Chevere\Components\App\Contracts\AppContract;

interface ControllerContract
{
    public function __construct(AppContract $app);

    public function getContent(): string;

    public function content(): string;
}
