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

namespace Chevere\Interfaces\Filesystem;

use Chevere\Exceptions\Core\InvalidArgumentException;

/**
 * Describes the component in charge of providing basename handling.
 */
interface BasenameInterface
{
    public const MAX_LENGTH_BYTES = 255;

    /**
     * @throws InvalidArgumentException if $basename exceed MAX_LENGTH_BYTES
     */
    public function __construct(string $basename);

    public function toString(): string;

    public function extension(): string;

    public function name(): string;
}
