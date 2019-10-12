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

namespace Chevere\Contracts\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Chevere\Console\Console;
use Chevere\Contracts\App\BuilderContract;

interface CommandContract
{
    const ARGUMENT_REQUIRED = InputArgument::REQUIRED;
    const ARGUMENT_OPTIONAL = InputArgument::OPTIONAL;
    const ARGUMENT_IS_ARRAY = InputArgument::IS_ARRAY;

    const OPTION_NONE = InputOption::VALUE_NONE;
    const OPTION_REQUIRED = InputOption::VALUE_REQUIRED;
    const OPTION_OPTIONAL = InputOption::VALUE_OPTIONAL;
    const OPTION_IS_ARRAY = InputOption::VALUE_IS_ARRAY;

    const NAME = '';
    const DESCRIPTION = '';
    const HELP = '';

    const ARGUMENTS = [];
    const OPTIONS = [];

    public function __construct(Console $console);

    public function console(): Console;

    public function symfony(): SymfonyCommandContract;

    public function callback(BuilderContract $builder): int;
}
