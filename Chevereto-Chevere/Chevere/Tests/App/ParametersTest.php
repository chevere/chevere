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

use Chevere\Components\App\Exceptions\ParametersDuplicatedException;
use Chevere\Components\App\Exceptions\ParametersWrongKeyException;
use Chevere\Components\App\Exceptions\ParametersWrongTypeException;
use Chevere\Components\App\Parameters;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Path\Path;
use Chevere\Contracts\App\ParametersContract;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function testConstructorWrongKey(): void
    {
        $arrayFile = new ArrayFile(
            new Path('parameters/wrongKey.php')
        );
        $this->expectException(ParametersWrongKeyException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWrongRoutesType(): void
    {
        $arrayFile = new ArrayFile(
            new Path('parameters/wrongRoutesType.php')
        );
        $this->expectException(ParametersWrongTypeException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWrongApiType(): void
    {
        $arrayFile = new ArrayFile(
            new Path('parameters/wrongApiType.php')
        );
        $this->expectException(ParametersWrongTypeException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWithRoutes(): void
    {
        $arrayFile = new ArrayFile(
            new Path('parameters/routes.php')
        );
        $parameters = new Parameters($arrayFile);
        $this->assertSame(true, $parameters->hasParameters());
        $this->assertSame(true, $parameters->hasRoutes());
        $this->assertSame($arrayFile->toArray()[ParametersContract::KEY_ROUTES], $parameters->routes());
    }

    public function testWithDuplicatedAddedRoutePaths(): void
    {
        $arrayFile = new ArrayFile(
            new Path('parameters/routes.php')
        );
        $this->expectException(ParametersDuplicatedException::class);
        $parameters = new Parameters($arrayFile);
        $parameters = $parameters->withAddedRoutePaths(new Path('routes/test.php'));
    }
}
