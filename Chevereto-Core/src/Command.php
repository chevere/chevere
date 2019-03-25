<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand implements Interfaces\CommandInterface
{
    /**
     * Provide input arguments contants shortcuts.
     */
    const ARGUMENT_REQUIRED = InputArgument::REQUIRED;
    const ARGUMENT_OPTIONAL = InputArgument::OPTIONAL;
    const ARGUMENT_IS_ARRAY = InputArgument::IS_ARRAY;

    /**
     * Provide input options contants shortcuts.
     */
    const OPTION_NONE = InputOption::VALUE_NONE;
    const OPTION_REQUIRED = InputOption::VALUE_REQUIRED;
    const OPTION_OPTIONAL = InputOption::VALUE_OPTIONAL;
    const OPTION_IS_ARRAY = InputOption::VALUE_IS_ARRAY;

    // protected $input;
    // protected $output;
    // protected $io;
    // protected $logger;
    protected $cli;

    public function __construct(Cli $cli)
    {
        // $this->logger = $logger;
        $this->cli = $cli;
        parent::__construct();
    }

    /**
     * Execute the command before "app".
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $this->input = $input;
        // $this->output = $output;
        // $this->io = new SymfonyStyle($input, $output);
        $this->getCli()->setCommand($this);
    }

    /**
     * Callback contains the actual command in-app instructions.
     */
    public function callback(App $app)
    {
        // TODO: Deberia ser LogicException
        throw new CoreException('You must override the '.__FUNCTION__.'() method in the concrete command class.');
    }

    /**
     * Get cli.
     */
    public function getCli(): Cli
    {
        return $this->cli;
    }
}
