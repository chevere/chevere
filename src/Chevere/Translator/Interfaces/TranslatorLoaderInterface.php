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

use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Throwable\Exceptions\DomainException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Gettext\TranslatorInterface;

/**
 * Describes the component in charge of load php translations.
 */
interface TranslatorLoaderInterface
{
    /**
     * @throws DirNotExistsException
     */
    public function __construct(DirInterface $dir);

    public function dir(): DirInterface;

    /**
     * @throws InvalidArgumentException If $locale doesn't exists.
     * @throws DomainException If $domain doesn't exists.
     * @throws LogicException If unable to load translator.
     */
    public function getTranslator(string $locale, string $domain): TranslatorInterface;
}
