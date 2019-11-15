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

use InvalidArgumentException;
use LogicException;
use Chevere\Components\Message\Message;
use function ChevereFn\stringStartsWithNumeric;

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
        $this->assertFormat();
        $this->assertRegex();
    }

    public function bind(Route $route)
    {
        $this->route = $route;
        $this->validateRoutePathMatch();
        $this->validateRouteUniqueWildcard();
    }

    private function assertFormat(): void
    {
        if (stringStartsWithNumeric($this->wildcardName)) {
            throw new InvalidArgumentException(
                (new Message("String %string% shouldn't start with a numeric value"))
                    ->code('%string%', $this->wildcardName)
                    ->toString()
            );
        }
        if (!preg_match('/^[a-z0-9_]+$/i', $this->wildcardName)) {
            throw new InvalidArgumentException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->wildcardName)
                    ->toString()
            );
        }
    }

    private function assertRegex(): void
    {
        if (!$this->validateRegex('/' . $this->regex . '/')) {
            throw new InvalidArgumentException(
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

    private function validateRoutePathMatch(): void
    {
        $noWildcard = false === strpos($this->route->path(), "{{$this->wildcardName}}");
        // $noOptionalWildcard = false === strpos($this->route->path(), '{' . "$this->wildcardName?" . '}');
        // if ($noWildcard && $noOptionalWildcard) {
        if ($noWildcard) {
            throw new LogicException(
                (new Message("Wildcard %wildcard% doesn't exists in %path%"))
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
                (new Message('Where clause for %wildcard% wildcard has been already declared'))
                    ->code('%wildcard%', $this->wildcardString)
                    ->toString()
            );
        }
    }
}
