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

use Chevereto\Core\App;
use Chevereto\Core\Load;
use Chevereto\Core\Message;
use Chevereto\Core\Path;
use Chevereto\Core\Command;
use Chevereto\Core\File;
use Chevereto\Core\Utils\Dump;
use Chevereto\Core\Utils\Str;
use ReflectionMethod;
use ReflectionFunction;

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
        $callableInput = $cli->getInput()->getArgument('callable');

        $isCallable = is_callable($callableInput);
        // $classExists = class_exists($callableInput);
        if ($isCallable) {
            $callable = $callableInput;
            $callableSome = $callableInput;
        } else {
            $callableFilepath = Path::fromHandle($callableInput);
            if (false == File::exists($callableFilepath)) {
                $io->error(sprintf('Unable to locate callable %s', $callableInput));

                return 0;
            }
            $callableSome = $callableFilepath;
            $callable = Load::php($callableFilepath);
            if (false == is_callable($callable)) {
                $io->error(
                    (new Message('Expecting %t return type, %s provided in %f'))
                        ->code('%t', 'callable')
                        ->code('%s', gettype($callable))
                        ->code('%f', $callableInput)
                );

                return 0;
            }
        }
        if (is_object($callable)) {
            $method = '__invoke';
        } else {
            if (Str::contains('::', $callable)) {
                $callableExplode = explode('::', $callable);
                $callable = $callableExplode[0];
                $method = $callableExplode[1];
            }
        }
        if (isset($method)) {
            $reflection = new ReflectionMethod($callable, $method);
        } else {
            $reflection = new ReflectionFunction($callable);
        }

        $io->block($callableSome, 'INSPECTED', 'fg=black;bg=green', ' ', true);

        $resource = [];
        if (isset($callableFilepath)) {
            $dir = Path::relative(dirname($callableFilepath), App::APP);
            do {
                $resourceFilePathRelative = $dir.'/resource.json';
                $resourceFilePath = App\PATH.$resourceFilePathRelative;
                if (File::exists($resourceFilePath) && $resourceString = file_get_contents($resourceFilePath)) {
                    $resourceArr = json_decode($resourceString, true)['wildcards'] ?? [];
                    $resource = array_merge($resourceArr, $resource);
                }
                $dir = $dir ? dirname($dir) : '.';
            } while ($dir != '.');
        }

        $arguments = [];
        if ($reflection->getDeclaringClass()->isInternal()) {
            $io->note('Cannot determine default value for internal functions');
        }
        $i = 0;
        foreach ($reflection->getParameters() as $parameter) {
            $aux = null;
            if ($parameter->getType()) {
                $aux .= $parameter->getType().' ';
            }
            $aux .= '$'.$parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                $aux .= ' = '.($parameter->getDefaultValue() ?? 'null');
            }
            if (null != $resource && $res = $resource[$parameter->getName()] ?? null) {
                $aux .= ' '.Dump::wrap(Dump::_OPERATOR, '--description '.$res['description'].' --regex '.$res['regex']);
            }
            $arguments[] = "#$i $aux";
            ++$i;
        }
        if (null != $arguments) {
            $io->text(['<fg=yellow>Arguments:</>']);
            $io->listing($arguments);
        } else {
            $io->text(['<fg=yellow>No arguments</>', null]);
        }

        return 1;
    }
}
