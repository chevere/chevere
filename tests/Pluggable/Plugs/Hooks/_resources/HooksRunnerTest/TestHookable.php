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

namespace Chevere\Tests\Pluggable\Plugs\Hooks\_resources\HooksRunnerTest;

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Pluggable\PluggableAnchors;
use Chevere\Components\Pluggable\Plugs\Hooks\Traits\PluggableHooksTrait;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;
use Chevere\Interfaces\Pluggable\Plugs\Hooks\PluggableHooksInterface;

class TestHookable implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    private string $string;

    private PathInterface $path;

    public function __construct()
    {
        $this->string = '';
    }

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors())
            ->withAdded('string')
            ->withAdded('path')
            ->withAdded('type');
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->hook('string', $string);
        $this->hook('type', $string);
        $this->string = $string;
    }

    public function setPath(PathInterface $path): void
    {
        $this->hook('path', $path);
        $this->path = $path;
    }

    public function string(): string
    {
        return $this->string;
    }

    public function path(): PathInterface
    {
        return $this->path;
    }
}
