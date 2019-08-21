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

use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Console\CliContract;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\BaseCommandContract;

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

    /** @var CliContract */
    protected $cli;

    /** @var BaseCommandContract */
    protected $baseCommand;

    final public function __construct(CliContract $cli)
    {
        $this->cli = $cli;
        $this->baseCommand = new BaseCommand($cli);
        $this->baseCommand->setCommand($this);
    }

    final public function baseCommand(): BaseCommandContract
    {
        return $this->baseCommand;
    }

    final public function configure()
    {
        $this->baseCommand
            ->setName(static::NAME)
            ->setDescription(static::DESCRIPTION)
            ->setHelp(static::HELP);
        foreach (static::ARGUMENTS as $arguments) {
            $this->baseCommand->addArgument(...$arguments);
        }
        foreach (static::OPTIONS as $options) {
            $this->baseCommand->addOption(...$options);
        }
    }

    public function callback(LoaderContract $loader)
    {
        throw new LogicException('You must override the ' . __FUNCTION__ . '() method in the concrete command class.');
    }
}
