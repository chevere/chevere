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

namespace Chevere\Tests\Route;

use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\PathUriWildcards;
use PHPUnit\Framework\TestCase;

final class PatUriWildcardsTest extends TestCase
{
    public function testConstructPathUriWithoutWildcard(): void
    {
        $pathUri = new PathUri('/test');
        $this->expectException(WildcardNotFoundException::class);
        new PathUriWildcards($pathUri);
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        $pathUri = new PathUri('/{wildcard}');
        $pathUriWildcards = new PathUriWildcards($pathUri);
        $this->assertSame($pathUri, $pathUriWildcards->pathUri());
    }
}
