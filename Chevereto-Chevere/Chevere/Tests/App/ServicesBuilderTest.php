<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\App\ServicesBuilder;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Http\Response;
use Chevere\Components\Path\Path;
use Chevere\Contracts\App\ServicesContract;
use PHPUnit\Framework\TestCase;

final class ServicesBuilderTest extends TestCase
{
  public function testConstruct(): void
  {
    $response = new Response();
    $services = new Services();
    $app = new App($services, $response);
    $build = new Build($app);
    $arrayFile = new ArrayFile(
      new FilePhp(
        new File(
          new Path('parameters/empty.php')
        )
      )
    );
    $parameters = new Parameters($arrayFile);
    $servicesBuilder = new ServicesBuilder($build, $parameters);
    $this->assertInstanceOf(ServicesContract::class, $servicesBuilder->services());
  }
}
