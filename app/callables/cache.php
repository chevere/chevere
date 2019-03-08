<?php
declare(strict_types=1);

namespace Chevereto\Core;

use App\User;

return new class extends Controller {
    public function __invoke(User $uno = null)
    {
        // throw new CoreException('pico');
        dd('Hola mundo!', $uno, $dos);
    }
};
return function (User $uno = null, string $dos = 'dos') {
    dd('Hola mundo!', $uno, $dos);
};
