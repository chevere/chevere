<?php

declare(strict_types=1);

namespace App\Api\Users\Friends;

use App\Api\Users\Resource;
use Chevereto\Core\ControllerRelationship;

abstract class Relationship extends ControllerRelationship
{
    const RELATIONSHIP = Resource::class;
}
