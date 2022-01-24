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

namespace Chevere\Pluggable;

use Chevere\ClassMap\Interfaces\ClassMapInterface;
use function Chevere\Filesystem\filePhpReturnForPath;
use function Chevere\Filesystem\varForFilePhpReturn;
use Chevere\Message\Message;
use Chevere\Pluggable\Exceptions\PluggableNotRegisteredException;
use Chevere\Pluggable\Exceptions\PlugsFileNotExistsException;
use Chevere\Pluggable\Interfaces\PluginsInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Type\Type;
use function Chevere\VarSupport\deepCopy;

/**
 * Pluggable -> plugs.php interaction
 */
final class Plugins implements PluginsInterface
{
    private string $plugsPath;

    public function __construct(
        private ClassMapInterface $classMap
    ) {
    }

    public function clonedClassMap(): ClassMapInterface
    {
        return deepCopy($this->classMap);
    }

    public function getPlugsQueue(string $pluggableName): PlugsQueueInterface
    {
        $this->assertSetPlugsPath($pluggableName);
        $this->assertPlugsPath();
        $fileReturn = filePhpReturnForPath($this->plugsPath);
        /**
         * @var PlugsQueueInterface $var
         */
        return varForFilePhpReturn($fileReturn, new Type(PlugsQueueInterface::class));
    }

    private function assertSetPlugsPath(string $pluggableName): void
    {
        try {
            $this->plugsPath = $this->classMap->key($pluggableName);
        } catch (OutOfBoundsException $e) {
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
