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

namespace Chevere\Components\Plugin;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\ClassMap\ClassNotMappedException;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Plugin\PluggableNotRegisteredException;
use Chevere\Exceptions\Plugin\PlugsFileNotExistsException;
use Chevere\Exceptions\Plugin\PlugsQueueInterfaceException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Chevere\Interfaces\Plugin\PluginsInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use LogicException;
use Throwable;
use TypeError;
use function Chevere\Components\Filesystem\getFilePhpReturnFromString;
use function DeepCopy\deep_copy;

/**
 * Pluggable -> plugs.php interaction
 */
final class Plugins implements PluginsInterface
{
    private ClassMap $classMap;

    private string $plugsPath;

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
        $this->assertSetPlugsPath($pluggableName);
        $this->assertPlugsPath();
        try {
            $fileReturn = getFilePhpReturnFromString($this->plugsPath)
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
                    ->code('%filePath%', $this->plugsPath)
                    ->code('%type%', PlugsQueueInterface::class)
            );
        }
    }

    private function assertSetPlugsPath(string $pluggableName): void
    {
        try {
            $this->plugsPath = $this->classMap->get($pluggableName);
        } catch (ClassNotMappedException $e) {
            throw new PluggableNotRegisteredException($e->message());
        }
    }

    private function assertPlugsPath(): void
    {
        if (stream_resolve_include_path($this->plugsPath) === false) {
            throw new PlugsFileNotExistsException(
                (new Message("File %fileName% doesn't exists"))
                    ->code('%fileName%', $this->plugsPath)
            );
        }
    }
}
