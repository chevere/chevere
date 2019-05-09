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

namespace Chevereto\Chevere;

/**
 * Abstract class used for API resourced Controllers.
 */
abstract class ControllerResource extends Controller implements Interfaces\ControllerResourceInterface
{
    // protected static $description = 'Describes the endpoint resource.';

    protected static $resources = [];

    public static function getResourceName(): string
    {
        return array_keys(static::getResources())[0];
    }
}
