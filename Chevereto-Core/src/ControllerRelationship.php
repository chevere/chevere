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

namespace Chevereto\Core;

/**
 * Abstract class used for API resourced Controllers with relationships.
 */
abstract class ControllerRelationship extends Controller implements Interfaces\ControllerRelationshipInterface
{
    protected static $description = 'Describes endpoint relationship.';

    protected static $relatedResource = null;

    public static function getRelatedResource(): ?string
    {
        return static::$relatedResource ?? null;
    }
}
