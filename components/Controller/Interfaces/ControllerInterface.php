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

namespace Chevere\Components\Controller\Interfaces;

use Chevere\Components\App\Interfaces\AppInterface;

interface ControllerInterface
{
    public function __construct(AppInterface $app);

    public function getContent(): string;

    public function content(): string;
}
