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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Traits\ControllerResponseTrait;
use Chevere\Interfaces\Controller\ControllerResponseSuccessInterface;

final class ControllerResponseSuccess implements ControllerResponseSuccessInterface
{
    use ControllerResponseTrait;

    public function withData(array $data): ControllerResponseSuccessInterface
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }
}
