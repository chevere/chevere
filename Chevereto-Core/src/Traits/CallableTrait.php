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

namespace Chevereto\Core\Traits;

use Chevereto\Core\File;
use Chevereto\Core\Path;
use Chevereto\Core\Message;
use Chevereto\Core\CoreException;
use Chevereto\Core\Utils\Str;

trait CallableTrait
{
    // callableString = 'TheCallable::fn, Class, functionName)
    public function getCallable(string $callableString): callable
    {
        $classExists = class_exists($callableString);
        $isCallable = is_callable($callableString);
        if ($classExists || $isCallable) {
            $callable = $classExists ? new $callableString() : $callableString;
        } else {
            $callable = include $callableString;
        }
        if (is_callable($callable) == false) {
            throw new CoreException(
                (new Message('Expected %s callable, %t provided in %f.'))
                    ->code('%s', '$callable')
                    ->code('%t', gettype($callable))
                    ->code('%f', $callableString)
            );
        }

        return $callable;
    }

    /**
     * Retuns the callable some (callable string, callable relative filepath).
     *
     * @param string $callableString a callable string
     */
    public function getCallableSome(string $callableString): ?string
    {
        if (is_callable($callableString)) {
            return $callableString;
        } else {
            if (class_exists($callableString)) {
                if (method_exists($callableString, '__invoke')) {
                    return $callableString;
                } else {
                    throw new CoreException(
                        (new Message('Missing %s method in class %c'))
                            ->code('%s', '__invoke')
                            ->code('%c', $callableString)
                    );
                }
            } else {
                $callableFile = Path::fromHandle($callableString);
                $this->checkCallableFile($callableFile);

                return Path::relative($callableFile);
            }
        }
    }

    /**
     * Same as ::getCallableSome() but this returns an absolute path for callable files.
     */
    // public function getCallableSomeAbsolute(string $callable): ?string
    // {
    //     $callableSome = $this->getCallableSome($callable);
    //     if (null != $callableSome && Str::endsWith('.php', $callableSome)) {
    //         return Path::absolute($callableSome);
    //     } else {
    //         return $callableSome;
    //     }
    // }

    /**
     * Checks if a callable file exists.
     */
    protected function checkCallableFile(string $callableFile)
    {
        // Check callable existance
        if (false == File::exists($callableFile, true)) {
            throw new CoreException(
                (new Message("Callable %s doesn't exists."))
                    ->code('%s', $callableFile)
            );
        }
        // Had to make this sandwich since we are calling an anon callable.
        $errorLevel = error_reporting();
        error_reporting($errorLevel ^ E_NOTICE);
        $anonCallable = include $callableFile;
        error_reporting($errorLevel);
        // Check callable
        if (is_callable($anonCallable) == false) {
            throw new CoreException(
            (new Message('File %f is not a valid %t.'))
                ->code('%f', $callableFile)
                ->code('%t', 'callable')
            );
        }
    }
}
