<?php
declare(strict_types=1);

namespace Chevereto\Core;

return new class extends Controller {
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
};
