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

namespace Chevere\Components\Console\Interfaces;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Chevere\Components\App\Interfaces\BuilderInterface;

interface CommandInterface
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

    public function __construct(ConsoleInterface $console);

    public function console(): ConsoleInterface;

    public function symfony(): SymfonyCommandInterface;

    public function getArgumentString(string $argument): string;

    public function getArgumentArray(string $argument): array;

    public function getOptionString(string $option): string;

    public function getOptionArray(string $option): array;

    public function callback(BuilderInterface $builder): int;
}
