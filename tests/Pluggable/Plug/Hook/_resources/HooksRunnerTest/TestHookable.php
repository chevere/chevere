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

namespace Chevere\Tests\Pluggable\Plug\Hook\_resources\HooksRunnerTest;

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Components\Pluggable\PluggableAnchors;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\PluggableHooksInterface;
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;

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
