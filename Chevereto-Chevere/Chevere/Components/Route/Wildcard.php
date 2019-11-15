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
use Chevere\Contracts\Route\WildcardContract;
use function ChevereFn\stringStartsWithNumeric;

final class Wildcard implements WildcardContract
{
    /** @var string */
    private $wildcardName;

    /** @var string */
    private $wildcardString;

    /** @var string */
    private $regex;

    /** @var string */
    private $path;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $wildcardName, string $regex)
    {
        $this->wildcardName = $wildcardName;
        $this->wildcardString = "{{$wildcardName}}";
        $this->regex = $regex;
        $this->assertFormat();
        $this->assertRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function assertPath(PathUri $pathUri): void
    {
        $this->path = $pathUri->path();
        $this->assertRoutePathMatch();
    }

    private function assertFormat(): void
    {
        if (stringStartsWithNumeric($this->wildcardName)) {
            throw new WildcardStartWithNumberException(
                (new Message("String %string% shouldn't start with a numeric value"))
                    ->code('%string%', $this->wildcardName)
                    ->toString()
            );
        }
        if (!preg_match(WildcardContract::ACCEPTED_CHARS_REGEX, $this->wildcardName)) {
            throw new WildcardInvalidCharsException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->wildcardName)
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

    private function assertRoutePathMatch(): void
    {
        $noWildcard = false === strpos($this->path, "{{$this->wildcardName}}");
        if ($noWildcard) {
            throw new WildcardNotFoundException(
                (new Message("Wildcard %wildcard% doesn't exists in route %path%"))
                    ->code('%wildcard%', $this->wildcardString)
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
