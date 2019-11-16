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
    private function getWildcard(): WildcardContract
    {
        return new Wildcard('test', '[a-z]+');
    }

    public function testConstructWildcardStartsWithInvalidChar(): void
    {
        $this->expectException(WildcardStartWithNumberException::class);
        new Wildcard('0test', '');
    }

    public function testConstructWildcardInvalidChars(): void
    {
        $this->expectException(WildcardInvalidCharsException::class);
        new Wildcard('t{e/s}t', '');
    }

    public function testConstructInvalidRegex(): void
    {
        $this->expectException(WildcardInvalidRegexException::class);
        new Wildcard('test', '$?');
    }

    public function testAssertPathWildcardNotExists(): void
    {
        $this->expectException(WildcardNotFoundException::class);
        $this->getWildcard()
            ->assertPath(
                new PathUri('/')
            );
    }

    public function testAssertPath(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getWildcard()
            ->assertPath(
                new PathUri('/{test}')
            );
    }
}
