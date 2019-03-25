<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

class RouteWildcard
{
    protected $description = null;
    protected $regex;

    public function __construct(string $description = null, string $regex = null)
    {
        $this->description = $description;
        $this->regex = $regex ?? Route::REGEX_WILDCARD_WHERE;
    }

    public function regex(): ?string
    {
        return $this->regex;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'regex' => $this->regex,
        ];
    }
}
class RouteWildcardException extends Route
{
}
