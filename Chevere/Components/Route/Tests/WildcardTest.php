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

use Chevere\Components\Route\WildcardMatch;
use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\Interfaces\WildcardInterface;
use PHPUnit\Framework\TestCase;

final class WildcardTest extends TestCase
{
    public function testConstructWildcardStartsWithInvalidChar(): void
    {
        $this->expectException(WildcardStartWithNumberException::class);
        new Wildcard('0test');
    }

    public function testConstructWildcardInvalidChars(): void
    {
        $this->expectException(WildcardInvalidCharsException::class);
        new Wildcard('t{e/s}t');
    }

    public function testConstruct(): void
    {
        $name = 'test';
        $wildcard = new Wildcard($name);
        $regexMatchDefault = new WildcardMatch(WildcardInterface::REGEX_MATCH_DEFAULT);
        $this->assertSame($name, $wildcard->name());
        $this->assertSame("{{$name}}", $wildcard->toString());
        $this->assertSame($regexMatchDefault->toString(), $wildcard->match()->toString());
    }

    public function testWithRegex(): void
    {
        $name = 'test';
        $regexMatch = new WildcardMatch('[a-z]+');
        $wildcard = (new Wildcard($name))
            ->withMatch($regexMatch);
        $this->assertSame($name, $wildcard->name());
        $this->assertSame($regexMatch, $wildcard->match());
    }

    public function testAssertPathWildcardNotExists(): void
    {
        $this->expectException(WildcardNotFoundException::class);
        (new Wildcard('test'))
            ->assertPathUri(
                new PathUri('/')
            );
    }

    public function testAssertPath(): void
    {
        $this->expectNotToPerformAssertions();
        (new Wildcard('test'))
            ->assertPathUri(
                new PathUri('/{test}')
            );
    }
}
