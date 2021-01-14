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

use Chevere\Interfaces\Filesystem\DirInterface;

/**
 * Describes the component in charge of make a translator.
 */
interface TranslatorMakerInterface
{
    public function __construct(DirInterface $sourceDir, DirInterface $targetDir);

    public function sourceDir(): DirInterface;

    public function targetDir(): DirInterface;

    public function withMakeTranslation(string $locale, string $domain): self;
}
