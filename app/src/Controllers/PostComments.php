<?php

declare(strict_types=1);

namespace App\Controllers;

use Chevere\Controller\Controller;

// TODO: Need access to post parameters (+header)
class PostComments extends Controller
{
    public function __invoke()
    {
        dump($this);
        echo 'Hola sub/post world!';
        $this->extra();
    }

    public function extra()
    {
        echo ' extra output ';
    }
}
