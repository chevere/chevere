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

namespace Chevere\Components\Route;

use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\WildcardInvalidRegexException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\WildcardContract;
use function ChevereFn\stringStartsWithNumeric;

final class Wildcard implements WildcardContract
{
    /** @var string */
    private $name;

    /** @var string */
    private $wildcard;

    /** @var string */
    private $regex;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->wildcard = "{{$this->name}}";
        $this->assertName();
        $this->regex = WildcardContract::REGEX_MATCH_DEFAULT;
        $this->assertRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function withRegex(string $regex): WildcardContract
    {
        $new = clone $this;
        $new->regex = $regex;
        $new->assertRegex();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->wildcard;
    }

    /**
     * {@inheritdoc}
     */
    public function regex(): string
    {
        return $this->regex;
    }

    /**
     * {@inheritdoc}
     */
    public function assertPathUri(PathUriContract $pathUri): void
    {
        $noWildcard = false === strpos($pathUri->path(), $this->wildcard);
        if ($noWildcard) {
            throw new WildcardNotFoundException(
                (new Message("Wildcard %wildcard% doesn't exists in route %path%"))
                    ->code('%wildcard%', $this->wildcard)
                    ->code('%path%', $pathUri->path())
                    ->toString()
            );
        }
    }

    private function assertName(): void
    {
        if (stringStartsWithNumeric($this->name)) {
            throw new WildcardStartWithNumberException(
                (new Message("String %string% shouldn't start with a numeric value"))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
        if (!preg_match(WildcardContract::ACCEPT_CHARS_REGEX, $this->name)) {
            throw new WildcardInvalidCharsException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
    }

    private function assertRegex(): void
    {
        if (!$this->validateRegex('/' . $this->regex . '/')) {
            throw new WildcardInvalidRegexException(
                (new Message('Invalid regex pattern %regex%'))
                    ->code('%regex%', $this->regex)
                    ->toString()
            );
        }
    }

    private function validateRegex(string $regex): bool
    {
        set_error_handler(function () { }, E_WARNING);
        $return = false !== preg_match($regex, '');
        restore_error_handler();

        return $return;
    }
}
