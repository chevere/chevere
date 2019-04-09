<?php

declare(strict_types=1);

namespace App\Controllers;

use Chevereto\Core\Controller;

class PostComments extends Controller
{
    public function __invoke($parameters)
    {
        dump($parameters, $this->_input, $this->_routing);
        echo 'Hola sub/post world!';
        $this->extra();
    }

    public function extra()
    {
        echo ' extra output ';
    }
}
