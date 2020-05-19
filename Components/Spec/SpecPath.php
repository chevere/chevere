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

use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Spec\SpecPathInterface;
use Chevere\Components\Str\Exceptions\StrAssertException;
use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithException;
use Chevere\Components\Str\Exceptions\StrStartsWithException;
use Chevere\Components\Str\StrAssert;
use InvalidArgumentException;

final class SpecPath implements SpecPathInterface
{
    private string $pub;

    /**
     * @param string $pub /spec
     * @param PathInterface $path The filesystem path for $pub
     * @throws StrAssertException If invalid $pub format provided
     */
    public function __construct(string $pub)
    {
        $this->pub = $pub;
        $this->assertPub();
    }

    public function pub(): string
    {
        return $this->pub;
    }

    /**
     * @throws StrEmptyException if $child is empty
     * @throws StrContainsException if $child contains spaces, // or \
     * @throws StrStartsWithException if $child starts with /
     * @throws StrEndsWithException if $child ends with /
     * @throws InvalidArgumentException $if unable to getChild on PathInterface
     */
    public function getChild(string $child): SpecPathInterface
    {
        (new StrAssert($child))
            ->notEmpty()
            ->notContains(' ')
            ->notStartsWith('/')
            ->notContains('//')
            ->notContains('\\')
            ->notEndsWith('/');

        return new self(rtrim($this->pub, '/') . '/' . $child);
    }

    /**
     *
     * @throws StrEmptyException if $pub is empty
     * @throws StrContainsException if $pub contains spaces, // or \
     * @throws StrNotStartsWithException if $pub not starts with /
     * @throws StrEndsWithException if $pub ends with /
     */
    private function assertPub(): void
    {
        if ($this->pub !== '/') {
            (new StrAssert($this->pub))
                ->notEmpty()
                ->notContains(' ')
                ->startsWith('/')
                ->notContains('//')
                ->notContains('\\')
                ->notEndsWith('/');
        }
    }
}
