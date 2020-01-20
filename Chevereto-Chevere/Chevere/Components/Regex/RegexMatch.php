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

namespace Chevere\Components\Regex;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Regex\Exceptions\RegexMatchException;
use Chevere\Components\Regex\Interfaces\RegexMatchInterface;

final class RegexMatch implements RegexMatchInterface
{
    /** @var string a regular expresion match statement */
    private string $match;

    /**
     * Creates a new instance.
     *
     * @throws RegexException if $match is an invalid regex matcher
     */
    public function __construct(string $match)
    {
        $this->match = $match;
        $this->assertRegex();
        $this->assertMatch();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->match;
    }

    private function assertRegex(): void
    {
        try {
            new Regex('#' . $this->match . '#');
        } catch (RegexException $e) {
            throw new RegexMatchException(
                (new Message('Invalid regex match expression provided %match%'))
                    ->code('%match%', $this->match)
                    ->toString()
            );
        }
    }

    private function assertMatch(): void
    {
        $match = str_replace(['\(', '\)'], null, $this->match);
        if (false !== strpos($match, '(') || false !== strpos($match, ')')) {
            throw new RegexMatchException(
                (new Message('Provided expresion %match% contains capture groups (remove any capture group)'))
                    ->code('%match%', $this->match)
                    ->toString()
            );
        }
    }
}
