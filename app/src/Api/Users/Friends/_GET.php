<?php

declare(strict_types=1);

namespace App\Api\Users\Friends;

class _GET extends Relationship
{
    protected static $description = 'Get {user} friends.';

    public function __construct($input)
    {
        $this->user = new User($input);
    }
}
