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

namespace Chevere\Components\App\Tests;

use Chevere\Components\App\Exceptions\ParametersDuplicatedException;
use Chevere\Components\App\Exceptions\ParametersWrongKeyException;
use Chevere\Components\App\Exceptions\ParametersWrongTypeException;
use Chevere\Components\App\Parameters;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\ArrayFile\Interfaces\ArrayFileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function getArrayFile(PathInterface $path): ArrayFileInterface
    {
        return
            new ArrayFile(
                new PhpFile(
                    new File($path)
                )
            );
    }

    public function testConstructorWrongKey(): void
    {
        $arrayFile = $this->getArrayFile(
            new AppPath('parameters/wrongKey.php')
        );
        $this->expectException(ParametersWrongKeyException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWrongRoutesType(): void
    {
        $arrayFile = $this->getArrayFile(
            new AppPath('parameters/wrongRoutesType.php')
        );
        $this->expectException(ParametersWrongTypeException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWrongApiType(): void
    {
        $arrayFile = $this->getArrayFile(
            new AppPath('parameters/wrongApiType.php')
        );
        $this->expectException(ParametersWrongTypeException::class);
        new Parameters($arrayFile);
    }

    public function testConstructorWithRoutes(): void
    {
        $arrayFile = $this->getArrayFile(
            new AppPath('parameters/routes.php')
        );
        $parameters = new Parameters($arrayFile);
        $this->assertSame(true, $parameters->hasParameters());
        $this->assertSame(true, $parameters->hasRoutes());
        $this->assertSame($arrayFile->array()[ParametersInterface::KEY_ROUTES], $parameters->routes());
    }

    public function testWithDuplicatedAddedRoutePaths(): void
    {
        $arrayFile = $this->getArrayFile(
            new AppPath('parameters/routes.php')
        );
        $this->expectException(ParametersDuplicatedException::class);
        (new Parameters($arrayFile))
            ->withAddedRoutePaths(new AppPath('routes/test.php'));
    }
}
