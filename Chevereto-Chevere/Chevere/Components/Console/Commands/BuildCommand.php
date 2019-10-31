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

namespace Chevere\Components\Console\Commands;

use LogicException;

use Chevere\Components\App\Exceptions\AlreadyBuiltException;
use Chevere\Components\Console\Command;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Maker;
use Chevere\Components\Time\TimeHr;
use Chevere\Contracts\App\BuilderContract;

use const Chevere\BOOTSTRAP_TIME;

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
        $timeStart = (int) hrtime(true);
        $this->builder = $builder;
        $this->assertBuilderParams();
        $title = 'App built';
        if ($this->builder->build()->isMaked()) {
            $title .= ' (not by this command)';
        } else {
            $this->builder = $this->builder
                ->withBuild(
                    $this->builder->build()
                    ->withRouterMaker(new Maker())
                    ->make()
                );
        }
        $timeEnd = (int) hrtime(true);
        $timeRelative = new TimeHr($timeEnd - $timeStart);
        $timeAbsolute = new TimeHr($timeEnd - BOOTSTRAP_TIME);
        $checksums = [];
        foreach ($this->builder->build()->checksums() as $name => $keys) {
            foreach ($keys as $key => $array) {
                $checksums[] = [$name, $key, $array['path'], substr($array['checksum'], 0, 8)];
            }
        }
        $this->console()->style()->success($title);
        $this->console()->style()->table(['Cache', 'Key', 'Path', 'Checksum'], $checksums);
        $this->console()->style()->writeln([
            '[Path] ' . $this->builder->build()->file()->path()->absolute(),
            '[Checksum] ' . $this->builder->build()->checkout()->checksum(),
            strtr('[Time] %relative% (%absolute%)', [
                '%relative%' => $timeRelative->toReadMs(),
                '%absolute%' => $timeAbsolute->toReadMs()
            ]),
        ]);
        return 0;
    }

    private function assertBuilderParams(): void
    {
        if (!$this->builder->build()->hasParameters()) {
            throw new LogicException(
                (new Message('Missing %class% parameters'))
                    ->code('%class%', get_class($this->builder))
                    ->toString()

            );
        }
    }
}
