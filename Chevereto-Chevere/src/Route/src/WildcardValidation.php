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

namespace Chevere\Route\src;

use LogicException;
use InvalidArgumentException;
use Chevere\Message;
use Chevere\Validate;
use Chevere\Utility\Str;
use Chevere\Contracts\Route\RouteContract;

final class WildcardValidation
{
    /** @var string */
    private $wildcardName;

    /** @var string */
    private $wildcardString;

    /** @var string */
    private $regex;

    /** @var string */
    public $uri;

    /** @var array */
    public $routeWheres;

    public function __construct(string $wildcardName, string $regex, RouteContract $route)
    {
        $this->wildcardName = $wildcardName;
        $this->wildcardString = "{{$wildcardName}}";
        $this->regex = $regex;
        $this->uri = $route->uri;
        $this->routeWheres = $route->wheres;
        $this->handleValidateFormat();
        $this->handleValidateMatch();
        $this->handleValidateUnique();
        $this->handleValidateRegex();
    }

    private function handleValidateFormat()
    {
        if (!$this->validateFormat($this->wildcardName)) {
            throw new InvalidArgumentException(
                (new Message("String %s must contain only alphanumeric and underscore characters and it shouldn't start with a numeric value."))
                    ->code('%s', $this->wildcardName)
                    ->toString()
            );
        }
    }

    private function validateFormat(string $wildcardName): bool
    {
        return !Str::startsWithNumeric($wildcardName) && preg_match('/^[a-z0-9_]+$/i', $wildcardName);
    }

    private function handleValidateMatch()
    {
        if (!$this->validateMatch($this->wildcardName, $this->uri)) {
            throw new LogicException(
                (new Message("Wildcard %s doesn't exists in %r."))
                    ->code('%s', $this->wildcardString)
                    ->code('%r', $this->uri)
                    ->toString()
            );
        }
    }

    private function validateMatch(string $wildcardName, string $routeKey): bool
    {
        return Str::contains("{{$wildcardName}}", $routeKey) || Str::contains('{'."$wildcardName?".'}', $routeKey);
    }

    private function handleValidateUnique()
    {
        if (!$this->validateUnique($this->wildcardName, $this->routeWheres)) {
            throw new LogicException(
                (new Message('Where clause for %s wildcard has been already declared.'))
                    ->code('%s', $this->wildcardString)
                    ->toString()
            );
        }
    }

    private function validateUnique(string $wildcardName, ?array $haystack): bool
    {
        return !isset($haystack[$wildcardName]);
    }

    private function handleValidateRegex()
    {
        if (!Validate::regex('/'.$this->wildcardName.'/')) {
            throw new InvalidArgumentException(
                (new Message('Invalid regex pattern %s.'))
                    ->code('%s', $this->regex)
                    ->toString()
            );
        }
    }
}
