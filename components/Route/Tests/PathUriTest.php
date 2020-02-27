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

use BadMethodCallException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedBracesException;
use Chevere\Components\Route\Exceptions\PathUriForwardSlashException;
use Chevere\Components\Route\Exceptions\PathUriInvalidCharsException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedWildcardsException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardRepeatException;
use Chevere\Components\Route\Exceptions\WildcardReservedException;
use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\WildcardMatch;
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
        $regex = $this->wrapRegex('^' . $path . '$');
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($path, $pathUri->key());
        $this->assertSame($regex, $pathUri->regex());
        $this->assertFalse($pathUri->hasWildcardCollection());
        $this->expectException(BadMethodCallException::class);
        $pathUri->uriFor([]);
    }

    public function testConstructWithWildcard(): void
    {
        $wildcard = new Wildcard('wildcard');
        $path = '/test/' . $wildcard->toString() . '/test';
        $key = '/test/{0}/test';
        $regex = $this->wrapRegex('^' . str_replace('{0}', '(' . $wildcard->match()->toString() . ')', $key) . '$');
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($key, $pathUri->key());
        $this->assertSame($regex, $pathUri->regex());
        $this->assertTrue($pathUri->hasWildcardCollection());
        $this->assertTrue($pathUri->wildcardCollection()->has($wildcard));
    }

    public function testConstructWithWildcards(): void
    {
        $wildcard1 = new Wildcard('wildcard1');
        $wildcard2 = new Wildcard('wildcard2');
        $path = '/test/' . $wildcard1->toString() . '/test/' . $wildcard2->toString();
        $key = '/test/{0}/test/{1}';
        $regex = $this->wrapRegex('^' . strtr($key, [
            '{0}' => '(' . $wildcard1->match()->toString() . ')',
            '{1}' => '(' . $wildcard2->match()->toString() . ')',
        ]) . '$');
        $pathUri = new PathUri($path);
        $this->assertSame($path, $pathUri->toString());
        $this->assertSame($key, $pathUri->key());
        $this->assertSame($regex, $pathUri->regex());
        $this->assertTrue($pathUri->hasWildcardCollection());
        $this->assertTrue($pathUri->wildcardCollection()->has($wildcard1));
        $this->assertTrue($pathUri->wildcardCollection()->has($wildcard2));
    }

    public function testWithNoApplicableWildcard(): void
    {
        $this->expectException(WildcardNotFoundException::class);
        (new PathUri('/test'))
            ->withWildcard(new Wildcard('wildcard'));
    }

    public function testRegex(): void
    {
        $match = '[a-z]+';
        $path = '/test/{id}';
        $regex = $this->wrapRegex('^' . str_replace('{id}', "($match)", $path) . '$');
        $pathUri = (new PathUri($path))
            ->withWildcard(
                (new Wildcard('id'))
                    ->withMatch(new WildcardMatch($match))
            );
        $this->assertSame($regex, $pathUri->regex());
    }

    public function testMatchFor(): void
    {
        $expected = [
            'id' => '123',
            'wildcard' => 'abc'
        ];
        $path = '/test/{id}/{wildcard}';
        $pathUri = new PathUri($path);
        $this->assertSame(
            $expected,
            $pathUri->matchFor(
                strtr('/test/id/wildcard', $expected)
            )
        );
        $this->assertSame([], $pathUri->matchFor('duh'));
    }

    public function testUriFor(): void
    {
        $id = 123;
        $wildcard = 'abc';
        $path = '/test/{id}/some/{wildcard}';
        $pathUri = new PathUri($path);
        $this->assertSame(
            strtr($path, [
                '{id}' => $id,
                '{wildcard}' => $wildcard,
            ]),
            $pathUri->uriFor([
                'id' => 123,
                'wildcard' => 'abc'
            ])
        );
        $this->expectException(PathUriUnmatchedBracesException::class);
        $pathUri->uriFor([]);
    }

    private function wrapRegex(string $pattern): string
    {
        return PathUriInterface::REGEX_DELIMITER_CHAR . $this->escapeRegex($pattern) . PathUriInterface::REGEX_DELIMITER_CHAR;
    }

    private function escapeRegex(string $pattern): string
    {
        return str_replace('/', '\/', $pattern);
    }
}
