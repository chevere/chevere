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

namespace Chevereto\Core\Traits;

use Chevereto\Core\File;
use Chevereto\Core\Path;
use Chevereto\Core\Message;
use Chevereto\Core\CoreException;

trait CallableTrait
{
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
                    return (string) $callableString;
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
     * Checks if a callable file exists.
     */
    protected function checkCallableFile(string $callableFile)
    {
        // Check callable existance
        if (!File::exists($callableFile, true)) {
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
        if (!is_callable($anonCallable)) {
            throw new CoreException(
            (new Message('File %f is not a valid %t.'))
                ->code('%f', $callableFile)
                ->code('%t', 'callable')
            );
        }
    }
}
