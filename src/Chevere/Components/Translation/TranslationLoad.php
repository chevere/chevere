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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Gettext\Translator;

final class TranslationLoad
{
    private DirInterface $dir;

    public function __construct(DirInterface $dir)
    {
        if (! $dir->exists()) {
            throw new InvalidArgumentException(
                (new Message("Directory %dir% doesn't exists"))
                    ->code('%dir%', $dir->path()->toString())
            );
        }
        $this->dir = $dir;
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function getTranslator(string $languageCode, string $messages): Translator
    {
        $dir = $this->dir->getChild($languageCode . '/');
        $dir->assertExists();
        $file = new File(
            $dir->path()->getChild("${messages}.php")
        );
        $file->assertExists();

        return (new Translator())->loadTranslations($file->path()->toString());
    }
}
