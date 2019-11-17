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

use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\WildcardInvalidRegexException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Wildcard;
use Chevere\Contracts\Route\WildcardContract;
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
        $this->assertSame($name, $wildcard->name());
        $this->assertSame(WildcardContract::REGEX_MATCH_DEFAULT, $wildcard->regex());
    }

    public function testWithInvalidRegex(): void
    {
        $this->expectException(WildcardInvalidRegexException::class);
        (new Wildcard('test'))
            ->withRegex('$?');
    }

    public function testWithRegex(): void
    {
        $name = 'test';
        $regex = '[a-z]+';
        $wildcard = (new Wildcard($name))
            ->withRegex($regex);
        $this->assertSame($name, $wildcard->name());
        $this->assertSame($regex, $wildcard->regex());
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
