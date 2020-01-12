<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Path;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathDoubleDotsException;
use Chevere\Components\Path\Exceptions\PathExtraSlashesException;
use Chevere\Components\Path\Exceptions\PathOmitRelativeException;
use Chevere\Components\Path\Interfaces\CheckFormatInterface;
use function ChevereFn\stringStartsWith;

final class CheckFormat implements CheckFormatInterface
{
    private string $path;

    /**
     * @throws PathInvalidException if the $path format is invalid
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertNoDoubleDots();
        $this->assertNoExtraSlashes();
    }

    /**
     * {@inheritdoc}
     */
    public function assertNotRelativePath(): void
    {
        if (stringStartsWith('./', $this->path)) {
            throw new PathOmitRelativeException(
                (new Message('Must omit %chars% for the path %path%'))
                    ->code('%chars%', './')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoDoubleDots(): void
    {
        if (false !== strpos($this->path, '../') || false !== strpos($this->path, '/..')) {
            throw new PathDoubleDotsException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', '../')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (false !== strpos($this->path, '//')) {
            throw new PathExtraSlashesException(
                (new Message('Path %path% contains extra-slashes'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
