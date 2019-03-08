<?php
declare(strict_types=1);

namespace App;

use function Chevereto\Core\dump;

return new class extends Controller {
    public function __invoke(User $var1 = null, $var2, $var0)
    {
        dump([
            '$var0' => $var0,
            '$var1'=> $var1,
            '$var2'=> $var2
        ]);
    }
};
// return function (User $var1 = null, $var2, $var0) {
//     Dumper::dump([
//         '$var0' => $var0,
//         '$var1'=> $var1,
//         '$var2'=> $var2
//     ]);
// };
