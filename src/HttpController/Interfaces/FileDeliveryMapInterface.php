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

namespace Chevere\HttpController\Interfaces;

/**
 * Describes the component in charge of mapping run response keys to
 * a file delivery sub-system.
 */
interface FileDeliveryMapInterface
{
    public const FILENAME = 'filename';

    public const PATHNAME = 'pathname';

    public function filename(): string;

    public function pathname(): string;
}
