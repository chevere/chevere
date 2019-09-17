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

namespace Chevere\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Console\ApplicationContract;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\SymfonyCommandContract;

/**
 * This is the base command of all Chevere commands.
 */
abstract class Command implements CommandContract
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

    /** @var Console */
    private $console;

    /** @var SymfonyCommandContract */
    private $symfony;

    final public function __construct(Console $console)
    {
        $this->console = $console;
        $this->symfony = new SymfonyCommand($this);
        $this->configure();
    }

    final public function console(): Console
    {
        return $this->console;
    }

    final public function symfony(): SymfonyCommandContract
    {
        return $this->symfony;
    }

    final public function getArgument(string $argument)
    {
        return $this->console->input()->getArgument($argument);
    }

    final public function getOption(string $option)
    {
        return $this->console->input()->getOption($option);
    }

    abstract public function callback(LoaderContract $loader): int;

    final protected function configure(): void
    {
        $this->symfony
            ->setName(static::NAME)
            ->setDescription(static::DESCRIPTION)
            ->setHelp(static::HELP);
        foreach (static::ARGUMENTS as $arguments) {
            $this->symfony->addArgument(...$arguments);
        }
        foreach (static::OPTIONS as $options) {
            $this->symfony->addOption(...$options);
        }
    }
}
