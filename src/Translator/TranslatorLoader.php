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

namespace Chevere\Translator;

use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\DomainException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Translator\Interfaces\TranslatorLoaderInterface;
use Gettext\Translator;
use Gettext\TranslatorInterface;
use LogicException;

final class TranslatorLoader implements TranslatorLoaderInterface
{
    public function __construct(
        private DirInterface $dir
    ) {
        $this->dir->assertExists();
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function getTranslator(string $locale, string $domain): TranslatorInterface
    {
        $dir = $this->dir->getChild($locale . '/');
        if (!$dir->exists()) {
            throw new InvalidArgumentException(
                (new Message("Locale %locale% doesn't exits"))
                    ->code('%locale%', $locale)
            );
        }
        $file = new File(
            $dir->path()->getChild("${domain}.php")
        );
        if (!$file->exists()) {
            throw new DomainException(
                (new Message("Domain %domain% doesn't exits"))
                    ->code('%domain%', $domain)
            );
        }

        try {
            return (new Translator())
                ->loadTranslations($file->path()->__toString());
        }
        // @codeCoverageIgnoreStart
        catch (\InvalidArgumentException $e) {
            throw new LogicException(previous: $e, message: new Message('Unable to load translator.'));
        }
        // @codeCoverageIgnoreEnd
    }
}
