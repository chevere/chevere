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

namespace Chevere\Filesystem\Interfaces;

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LengthException;
use Stringable;

/**
 * Describes the component in charge of providing filename handling.
 */
interface FilenameInterface extends Stringable
{
    public const MAX_LENGTH_BYTES = 255;

    /**
     * @throws InvalidArgumentException if $basename is invalid string
     * @throws LengthException if $basename exceed MAX_LENGTH_BYTES
     */
    public function __construct(string $basename);

    public function extension(): string;

    public function name(): string;
}
