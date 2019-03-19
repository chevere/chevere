<?php
declare(strict_types=1);

namespace Chevereto\Core;

use App\User;
use Chevereto\Core\CoreException;
use Chevereto\Core\App;

return new class extends Controller {
    public function __invoke(User $uno = null)
    {
        // throw new CoreException('eeee');
        dd('Hola mundo!');
    }
};
return function (User $uno = null, string $dos = 'dos') {
    dd('Hola mundo!', $uno, $dos);
};
