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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Route\Exceptions\PathUriUnmatchedBracesException;
use Chevere\Components\Route\Exceptions\PathUriForwardSlashException;
use Chevere\Components\Route\Exceptions\PathUriInvalidCharsException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedWildcardsException;
use Chevere\Components\Route\Exceptions\WildcardRepeatException;
use Chevere\Components\Route\Exceptions\WildcardReservedException;
use Chevere\Components\Route\PathUri;
use PHPUnit\Framework\TestCase;

final class PathUriTest extends TestCase
{
    public function testConstructNoForwardSlash(): void
    {
        $this->expectException(PathUriForwardSlashException::class);
        new PathUri('test');
    }

    public function testConstructIllegalChars(): void
    {
        $this->expectException(PathUriInvalidCharsException::class);
        new PathUri('//{{\\}} ');
    }

    public function testConstructNotMatchingBraces(): void
    {
        $this->expectException(PathUriUnmatchedBracesException::class);
        new PathUri('/test/{test/}/}/test');
    }

    public function testConstructWithInvalidWildcard(): void
    {
        $this->expectException(PathUriUnmatchedWildcardsException::class);
        new PathUri('/{wild-card}');
    }

    public function testConstructWithWildcardReserved(): void
    {
        $this->expectException(WildcardReservedException::class);
        new PathUri('/{0}');
    }

    public function testConstructWithWildcardTwiceSame(): void
    {
        $this->expectException(WildcardRepeatException::class);
        new PathUri('/test/{wildcard}/{wildcard}');
    }

    public function testConstruct(): void
    {
        $path = '/test';
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($path, $pathUri->key());
        $this->assertFalse($pathUri->hasWildcards());
    }

    public function testConstructWithWildcard(): void
    {
        $path = '/test/{wildcard}/test';
        $key = '/test/{0}/test';
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($key, $pathUri->key());
        $this->assertTrue($pathUri->hasWildcards());
        $this->assertSame(['wildcard'], $pathUri->wildcards());
    }

    public function testConstructWithWildcards(): void
    {
        $path = '/test/{wildcard1}/test/{wildcard2}';
        $key = '/test/{0}/test/{1}';
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($key, $pathUri->key());
        $this->assertTrue($pathUri->hasWildcards());
        $this->assertSame(['wildcard1', 'wildcard2'], $pathUri->wildcards());
    }
}
