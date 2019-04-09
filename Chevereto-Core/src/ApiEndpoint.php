<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Roave\BetterReflection\BetterReflection;

class ApiEndpoint
{
    /**
     * @param string $className A className implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $classInfo = (new BetterReflection())
            ->classReflector()
            ->reflect($className);
        // $load = Load::php($filepath);
        // $classMethods = $classInfo->getMethod('__construct');
        $classMethods = get_class_methods($classInfo);
        if (!$classInfo->implementsInterface(Interfaces\ControllerInterface::class)) {
        }
        dd($className);
    }
}
