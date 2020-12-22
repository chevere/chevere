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

namespace Chevere\Components\Router\RouteParsers;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use FastRoute\RouteParser\Std;
use Throwable;

/**
 * Strict version of `FastRoute\RouteParser\Std`, without optional routing.
 */
final class StrictStd extends Std
{
    /**
     * https://regexr.com/5j24s
     */
    const REGEX_PATH = '#^\/$|^\/(?:[^\/]+\/)*[^\/]*[^\/]$#';

    public function parse($route)
    {
        $matches = (new Regex(self::REGEX_PATH))->match($route);
        if($matches === []) {
            throw new InvalidArgumentException(
                (new Message("Route provided %provided% doesn't match regex %regex%"))
                    ->code('%provided%', $route)
                    ->code('%regex%', self::REGEX_PATH)
            );
        }

        try {
            $datas = parent::parse($route);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new InvalidArgumentException(
                (new Message('Unable to parse route %route%'))
                    ->code('%route%', $route),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
        if (count($datas) > 1) {
            throw new InvalidArgumentException(
                (new Message('Optional routing at route %route% is forbidden'))
                    ->code('%route%', $route)
            );
        }

        return $datas;
    }
}
