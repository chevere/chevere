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

namespace Chevere\Components\Plugs;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\ClassMap\Exceptions\ClassNotMappedException;
use Chevere\Components\ClassMap\Interfaces\ClassMapInterface;
use Chevere\Components\ExceptionHandler\Exceptions\Exception;
use Chevere\Components\ExceptionHandler\Exceptions\RuntimeException;
use Chevere\Components\Filesystem\FilePhpReturnFromString;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Exceptions\PluggableNotRegisteredException;
use Chevere\Components\Plugs\Exceptions\PlugsFileNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugsQueueInterfaceException;
use Chevere\Components\Plugs\Interfaces\PluginsInterface;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use LogicException;
use Throwable;
use TypeError;
use function DeepCopy\deep_copy;

/**
 * Pluggable -> plugs.php interaction
 */
final class Plugins implements PluginsInterface
{
    private ClassMap $classMap;

    public function __construct(ClassMapInterface $pluggablesToPlugs)
    {
        $this->classMap = $pluggablesToPlugs;
    }

    public function classMap(): ClassMapInterface
    {
        return deep_copy($this->classMap);
    }

    /**
     * @throws PluggableNotRegisteredException
     * @throws PlugsFileNotExistsException
     * @throws RuntimeException
     * @throws LogicException
     */
    public function getPlugsQueue(string $pluggableName): PlugsQueueInterface
    {
        try {
            $plugsPath = $this->classMap->get($pluggableName);
        } catch (ClassNotMappedException $e) {
            throw new PluggableNotRegisteredException($e->message());
        }
        if (stream_resolve_include_path($plugsPath) === false) {
            throw new PlugsFileNotExistsException(
                (new Message("File %fileName% doesn't exists"))
                    ->code('%fileName%', $plugsPath)
            );
        }
        try {
            $fileReturn = (new FilePhpReturnFromString($plugsPath))
                ->withStrict(false);
            /**
             * @var PlugsQueueInterface $var
             */
            $var = $fileReturn->var();
        } catch (Throwable $e) {
            throw new RuntimeException(
                $e instanceof Exception
                    ? $e->message()
                    : new Message($e->getMessage())
            );
        }
        try {
            return $var;
        } catch (TypeError $e) {
            throw new PlugsQueueInterfaceException(
                (new Message('Return of %filePath% is not of type %type%'))
                    ->code('%filePath%', $plugsPath)
                    ->code('%type%', PlugsQueueInterface::class)
            );
        }
    }
}
