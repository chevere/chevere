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

use Chevere\Components\Route\Exceptions\PathUriUnmatchedBracesException;
use Chevere\Components\Route\Exceptions\PathUriForwardSlashException;
use Chevere\Components\Route\Exceptions\PathUriInvalidCharsException;
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
        new PathUri('/test/{test/}/test');
    }
}
