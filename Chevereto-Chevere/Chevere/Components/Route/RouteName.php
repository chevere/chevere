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

use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Contracts\Route\RouteNameContract;

final class RouteName implements RouteNameContract
{
    /** @var string */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->name;
    }

    public function assertFormat(): void
    {
        if (!preg_match(RouteNameContract::REGEX, $this->name)) {
            throw new RouteInvalidNameException(
              (new Message('Expecting at least one alphanumeric, underscore, hypen or dot character, string %string% provided (regex %regex%)'))
                ->code('%string%', $this->name)
                ->code('%regex%', RouteNameContract::REGEX)
                ->toString()
        );
        }
    }
}
