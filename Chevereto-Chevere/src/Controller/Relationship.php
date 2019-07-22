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

namespace Chevere\Controller;

use Chevere\Interfaces\ControllerRelationshipInterface;

/**
 * Abstract class used for API resourced Controllers with relationships.
 */
abstract class Relationship extends Controller implements ControllerRelationshipInterface
{
    protected static $description = 'Describes endpoint relationship.';

    protected static $relatedResource = null;

    public static function getRelatedResource(): ?string
    {
        return static::$relatedResource ?? null;
    }
}
