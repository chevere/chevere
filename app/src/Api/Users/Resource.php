<?php

declare(strict_types=1);

namespace App\Api\Users;

use App\User;
use Chevereto\Chevere\ControllerResource;

abstract class Resource extends ControllerResource
{
    /** @var array Controller resources [propName => className] */
    protected static $resources = [
        'user' => User::class,
    ];
    /** @var User The user entity resource */
    protected $user;
}
