<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use LogicException;
use Chevere\Api\Maker;
use Chevere\Router\Router;
use PHPUnit\Framework\TestCase;

final class MakerTest extends TestCase
{
    /** @var MakerContractx */
    protected $maker;

    public function setUp(): void
    {
        $this->maker = new Maker(new Router());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCanRegisterApiPath(): void
    {
        $this->maker->register('src/Api/');
    }

    public function testCannotRegisterInvalidPath(): void
    {
        $this->expectException(LogicException::class);
        $this->maker->register('');
    }
}
