<?php

declare(strict_types=1);

namespace App\Api\Users\Friends;

use App\Api\Users\Resource;
use Chevere\Controller\Relationship as ControllerRelationship;

abstract class Relationship extends ControllerRelationship
{
    protected static $relatedResource = Resource::class;

    public function __invoke()
    {
    }
}
