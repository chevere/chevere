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

namespace Chevere\Console\Commands;

use Chevere\App\Exceptions\AlreadyBuiltException;
use Chevere\Console\Command;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Message\Message;
use LogicException;

/**
 * The BuildCommand builds the App.
 *
 * Usage:
 * php app/console build
 */
final class BuildCommand extends Command
{
    const NAME = 'build';
    const DESCRIPTION = 'Build the App';
    const HELP = 'This command builds the App';

    /** @var BuilderContract */
    private $builder;

    public function callback(BuilderContract $builder): int
    {
        $this->builder = $builder;
        $this->assertBuilderParams();
        $title = 'App built';
        try {
            $build = $this->builder->build()
                ->withParameters($this->builder->parameters());
            $this->builder = $this->builder
                ->withBuild($build);
        } catch (AlreadyBuiltException $e) {
            $title .= ' (not by this command)';
        }
        $checksums = [];
        foreach ($this->builder->build()->cacheChecksums() as $name => $keys) {
            foreach ($keys as $key => $array) {
                $checksums[] = [$name, $key, $array['path'], substr($array['checksum'], 0, 8)];
            }
        }
        $this->console()->style()->success($title);
        $this->console()->style()->table(['Cache', 'Key', 'Path', 'Checksum'], $checksums);
        $this->console()->style()->writeln([
            '[Path] ' . $this->builder->build()->pathHandle()->path(),
            '[Checksum] ' . $this->builder->build()->checkout()->checksum()
        ]);
        return 0;
    }

    private function assertBuilderParams(): void
    {
        if (!$this->builder->hasParameters()) {
            throw new LogicException(
                (new Message('Missing %class% %parameters%'))
                    ->code('%class%', get_class($this->builder))
                    ->code('%parameters%', 'parameters')
                    ->toString()

            );
        }
    }
}
