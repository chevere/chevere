<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Route;

use LogicException;
use InvalidArgumentException;
use Chevere\Message;
use Chevere\Validate;
use Chevere\Utility\Str;

final class Wildcard
{
    /** @var string */
    private $wildcardName;

    /** @var string */
    private $wildcardString;

    /** @var string */
    private $regex;

    /** @var Route */
    private $route;

    public function __construct(string $wildcardName, string $regex)
    {
        $this->wildcardName = $wildcardName;
        $this->wildcardString = "{{$wildcardName}}";
        $this->regex = $regex;
        $this->validateFormat();
        $this->validateRegex();
    }

    public function bind(Route $route)
    {
        $this->route = $route;
        $this->validateRoutePathMatch();
        $this->validateRouteUniqueWildcard();
    }

    private function validateFormat(): void
    {
        if (!(!Str::startsWithNumeric($this->wildcardName) && preg_match('/^[a-z0-9_]+$/i', $this->wildcardName))) {
            throw new InvalidArgumentException(
                (new Message("String %s must contain only alphanumeric and underscore characters and it shouldn't start with a numeric value."))
                    ->code('%s', $this->wildcardName)
                    ->toString()
            );
        }
    }

    private function validateRegex()
    {
        if (!Validate::regex('/'.$this->regex.'/')) {
            throw new InvalidArgumentException(
                (new Message('Invalid regex pattern %regex%.'))
                    ->code('%regex%', $this->regex)
                    ->toString()
            );
        }
    }

    private function validateRoutePathMatch(): void
    {
        if (!(Str::contains("{{$this->wildcardName}}", $this->route->path()) || Str::contains('{'."$this->wildcardName?".'}', $this->route->path()))) {
            throw new LogicException(
                (new Message("Wildcard %wildcard% doesn't exists in %path%."))
                    ->code('%wildcard%', $this->wildcardString)
                    ->code('%path%', $this->route->path())
                    ->toString()
            );
        }
    }

    private function validateRouteUniqueWildcard(): void
    {
        if (isset($this->route->wheres()[$this->wildcardName])) {
            throw new LogicException(
                (new Message('Where clause for %s wildcard has been already declared.'))
                    ->code('%s', $this->wildcardString)
                    ->toString()
            );
        }
    }
}
