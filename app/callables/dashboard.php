<?php

declare(strict_types=1);

namespace Chevereto\Core;

return new class() extends Controller {
    public function __invoke($algo = null, $sub = null): array
    {
        return [
            'algo' => $algo,
            'sub' => $sub,
            //   'this' => $this
        ];
    }
};

/*
 * Cuando se usa un lambda, no hay acceso a $this
 * TODO: $parameter name must match the router declaration!
 */
return function ($algo = null, $sub = null) use ($vars, $routing) {
    return [
        'algo' => $algo,
        'sub' => $sub,
        'vars' => $vars,
        'routing' => (array) $routing,
    ];
};
