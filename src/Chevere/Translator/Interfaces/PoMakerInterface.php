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

namespace Chevere\Translator\Interfaces;

use BadMethodCallException;
use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;

/**
 * Describes the component in charge of providing a `.po` maker.
 */
interface PoMakerInterface
{
    public function __construct(string $locale, string $domain);

    /**
     * @throws DirNotExistsException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function withScanFor(DirInterface $sourceDir): self;

    /**
     * @throws BadMethodCallException If called without scanner.
     * @throws DirUnableToCreateException If unable to create the target dir (if doesn't exists).
     * @throws FileUnableToRemoveException If unable to remove existing `.po` at target dir.
     * @throws LogicException If unable to create the translation file.
     */
    public function make(DirInterface $targetDir): void;
}
