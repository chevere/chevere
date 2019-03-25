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

namespace Chevereto\Core\Command;

use ReflectionMethod;
use Chevereto\Core\App;
use Chevereto\Core\Path;
use Chevereto\Core\Command;
use Chevereto\Core\File;
use Chevereto\Core\Message;
use Chevereto\Core\Utils\Dump;

/**
 * The InspectCommand allows to get callable information using CLI.
 *
 * Usage:
 * php app/console inspect <>
 */
class InspectCommand extends Command
{
    protected static $defaultName = 'inspect';

    protected function configure()
    {
        $this
          ->setDescription('Inspect any callable')
          ->setHelp('This command allows you to inspect any callable')
          ->addArgument('callable', Command::ARGUMENT_REQUIRED, 'The callable handle (name, fileHandle)');
    }

    /**
     * Inspect the target callable.
     */
    public function callback(App $app): int
    {
        $cli = $this->getCli();
        $io = $cli->getIo();
        $callable = $cli->getInput()->getArgument('callable');
        $callableFilePath = Path::fromHandle($callable);
        if (File::exists($callableFilePath) == false) {
            $io->error(
                (string) (new Message("Callable %s doesn't exists"))
                    ->strtr('%s', $callableFilePath)
            );

            return 1;
        }
        $controller = include $callableFilePath;
        $invoke = new ReflectionMethod($controller, '__invoke');
        $dir = Path::relative(dirname($callableFilePath), App::APP);
        $resource = [];
        do {
            // FIXME: Usar metodo para resolver $resourceFilePath
            $resourceFilePathRelative = $dir.'/resource.json';
            $resourceFilePath = App\PATH.$resourceFilePathRelative;
            if (File::exists($resourceFilePath) && $resourceString = file_get_contents($resourceFilePath)) {
                $resourceArr = json_decode($resourceString, true)['wildcards'] ?? [];
                $resource = array_merge($resourceArr, $resource);
            }
            $dir = $dir ? dirname($dir) : '.';
        } while ($dir != '.');
        //black, red, green, yellow, blue, magenta, cyan, white, default
        $io->text('<fg=blue>'.$callableFilePath.'</>');
        $arguments = [];
        if ($invoke->getDeclaringClass()->isInternal()) {
            $io->note('Cannot determine default value for internal functions');
        }
        $io->text([null, '<fg=yellow>Arguments:</>']);
        foreach ($invoke->getParameters() as $parameter) {
            $aux = null;
            if ($parameter->getType()) {
                $aux .= $parameter->getType().' ';
            }
            $aux .= '$'.$parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                $aux .= ' = '.($parameter->getDefaultValue() ?? 'null');
            }
            if ($resource != null && $res = $resource[$parameter->getName()] ?? null) {
                $aux .= ' '.Dump::wrap(Dump::_OPERATOR, '--desc '.$res['description'].' --regex '.$res['regex']);
            }
            $arguments[] = $aux;
        }
        $io->listing($arguments);

        return 1;
    }
}
