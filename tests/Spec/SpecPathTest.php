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

namespace Chevere\Tests\Spec;

use Chevere\Components\Spec\SpecDir;
use Chevere\Interfaces\Spec\SpecDirInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\dirForPath;

final class SpecPathTest extends TestCase
{
    private SpecDirInterface $specPath;

    public function setUp(): void
    {
        $this->specPath = new SpecDir(dirForPath('/spec/'));
    }

    public function testConstruct(): void
    {
        $pub = dirForPath('/spec/');
        $specPath = new SpecDir($pub);
        $this->assertSame($pub->path()->absolute(), $specPath->toString());
    }

    public function testGetChild(): void
    {
        $pub = '/spec/';
        $child = 'child';
        $specPath = new SpecDir(dirForPath($pub));
        $getChild = $specPath->getChild("$child/");
        $this->assertSame("$pub$child/", $getChild->toString());
        $this->assertSame($child, basename($getChild->toString()));
    }
}
