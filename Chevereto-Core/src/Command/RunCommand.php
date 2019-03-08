<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Command;

use Chevereto\Core\App;
use Chevereto\Core\File;
use Chevereto\Core\Path;
use Chevereto\Core\Command;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * The RunCommand allows to run any callable present in the app.
 *
 * Usage:
 * php app/console run <pathHandle>
 */
class RunCommand extends Command
{
    protected static $defaultName = 'run';
    protected function configure()
    {
        $this
            ->setDescription('Run any callable')
            ->setHelp('This command allows you to run any callable')
            ->addArgument('callable', Command::ARGUMENT_REQUIRED, 'The callable handle (name, fileHandle)')
            ->addOption(
                'argument',
                'a',
                Command::OPTION_OPTIONAL | Command::OPTION_IS_ARRAY,
                'Callable arguments (in declarative order)'
            );
    }
    /**
     * Run a callable.
     */
    public function callback(App $app) : int
    {
        $callable = $this->input->getArgument('callable');
        if (is_callable($callable) || class_exists($callable)) {
            $callableSome = $callable;
        } else {
            $callableSome = Path::fromHandle($callable);
            if (File::exists($callableSome) == false) {
                $this->io->error(sprintf('Unable to locate callable %s', $callable));
                return 0;
            }
        }
        $app->setArguments($this->input->getOption('argument'));
        $app->run($callableSome);
        return 1;
    }
}
