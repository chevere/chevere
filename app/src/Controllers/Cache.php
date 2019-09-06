<?php

declare(strict_types=1);

namespace App\Controllers;

use App\User;
use Chevere\Controller\Controller;
use Chevere\Http\Response;

class Cache extends Controller
{
    protected static $resources = [
        'user' => User::class,
    ];

    public function __invoke(string $llave, string $user, string $cert)
    {
        // $this->app->response()->setContent('eee');
        // dd(func_get_args(), ['llave' => $llave, 'user' => $user, 'cert' => $cert]);
    }

    public function render(): ?string
    {
        $response = $this->response;

        // return var_export($response->getData(), true);
    }
}
