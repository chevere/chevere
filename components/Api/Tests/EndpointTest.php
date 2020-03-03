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

namespace Chevere\Components\Api\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use PHPUnit\Framework\TestCase;

final class EndpointTest extends TestCase
{
    public function testWea(): void
    {
        // $path = new Path(__DIR__ . '/');
        // xdd($path);
        // $root = (new Dir(new Path(__DIR__ . '/')))->getChild('_resources/');
        // $method = include $root->path()->getChild('api/articles/{id}/Get.php')->absolute();
        // $method = $method->withRootDir($root);
        // xdd($method->path());
        // xdd($method->controller()('le gusta el ñé'));
        // $controller = new GetArticleController();
        // xdd($controller, $controller('al 🦖 le gusta el pico'));
        // xdd($method);
    }
}
