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

namespace Chevere\Components\Translator;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\DomainException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Translator\TranslatorLoaderInterface;
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
                ->loadTranslations($file->path()->toString());
        }
        // @codeCoverageIgnoreStart
        catch (\InvalidArgumentException $e) {
            throw new LogicException(previous: $e, message: new Message('Unable to load translator.'));
        }
        // @codeCoverageIgnoreEnd
    }
}
