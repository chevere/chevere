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

namespace Chevere\Components\Translation;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\DomainException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Gettext\Translator;
use Gettext\TranslatorInterface;

final class TranslatorLoader
{
    private DirInterface $dir;

    public function __construct(DirInterface $dir)
    {
        $dir->assertExists();
        $this->dir = $dir;
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function getTranslator(string $locale, string $domain): TranslatorInterface
    {
        $dir = $this->dir->getChild($locale . '/');
        $dir->assertExists();
        $file = new File(
            $dir->path()->getChild("${domain}.php")
        );
        $file->assertExists();

        try {
            return (new Translator())
                ->loadTranslations($file->path()->toString());
        } catch (\InvalidArgumentException $e) {
            throw new DomainException(
                (new Message($e->getMessage()))
            );
        }
    }
}
