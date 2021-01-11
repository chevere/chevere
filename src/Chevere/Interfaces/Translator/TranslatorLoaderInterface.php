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

namespace Chevere\Interfaces\Translator;

use Chevere\Exceptions\Core\DomainException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Interfaces\Filesystem\DirInterface;
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
