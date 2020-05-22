<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere;

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Writers\Writers;
use Chevere\Interfaces\Filesystem\PathInterface;
use function DeepCopy\deep_copy;

require 'vendor/autoload.php';

$rootDir = new DirFromString(__DIR__ . '/Chevere/TestApp/');

new BootstrapInstance(
    (new Bootstrap($rootDir, $rootDir->getChild('app/')))
        ->withCli(true)
        ->withDev(false)
);

new WritersInstance(new Writers);

// class Mutable
// {
//     private PathInterface $path;

//     // public function __construct(PathInterface $path)
//     // {
//     //     $this->path = $path;
//     // }

//     public function withPath(PathInterface $path): Mutable
//     {
//         $new = clone $this;
//         $new->path = $path;

//         return $new;
//     }

//     public function path(): PathInterface
//     {
//         return deep_copy($this->path);
//     }
// }
// $path = new Path(__FILE__);
// $mutable = (new Mutable)->withPath($path);

// xdd($path, $mutable->path());
