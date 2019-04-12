<?php

declare(strict_types=1);

namespace App\Controllers;

use App\User;
use Chevereto\Core\Controller;

class Cache extends Controller
{
    protected static $resources = [
        'user' => User::class,
    ];

    public function __invoke()
    {
    }

    public function render(): ?string
    {
        $response = $this->getResponse();

        return var_export($response->getData(), true);
    }
}
