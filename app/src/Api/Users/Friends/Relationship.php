<?php

declare(strict_types=1);

namespace App\Api\Users\Friends;

use App\Api\Users\Resource;
use Chevereto\Chevere\ControllerRelationship;

abstract class Relationship extends ControllerRelationship
{
    protected static $relatedResource = Resource::class;
}
