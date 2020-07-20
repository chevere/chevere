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

namespace Chevere\Components\Spec;

use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Spec\SpecPathInterface;
use Throwable;

final class SpecPath implements SpecPathInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        if ($this->path !== '/') {
            try {
                (new StrAssert($this->path))
                    ->notEmpty()
                    ->notContains(' ')
                    ->startsWith('/')
                    ->notContains('//')
                    ->notContains('\\')
                    ->notEndsWith('/');
            } catch (Throwable $e) {
                throw new InvalidArgumentException(
                    (new Message('Invalid argument %argument% provided'))
                        ->code('%argument%', $path),
                    0,
                    $e
                );
            }
        }
    }

    public function toString(): string
    {
        return $this->path;
    }

    public function getChild(string $childPath): SpecPathInterface
    {
        try {
            (new StrAssert($childPath))
                ->notEmpty()
                ->notContains(' ')
                ->notStartsWith('/')
                ->notContains('//')
                ->notContains('\\')
                ->notEndsWith('/');
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                (new Message('Invalid argument %argument% provided'))
                    ->code('%argument%', $childPath),
                0,
                $e
            );
        }

        return new self(rtrim($this->path, '/') . '/' . $childPath);
    }
}
