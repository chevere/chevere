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

namespace Chevere\Components\Pluggable;

use function Chevere\Components\Filesystem\filePhpReturnForPath;
use function Chevere\Components\Filesystem\varForFilePhpReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Pluggable\PluggableNotRegisteredException;
use Chevere\Exceptions\Pluggable\PlugsFileNotExistsException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Chevere\Interfaces\Pluggable\PluginsInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use function DeepCopy\deep_copy;

/**
 * Pluggable -> plugs.php interaction
 */
final class Plugins implements PluginsInterface
{
    private ClassMapInterface $classMap;

    private string $plugsPath;

    public function __construct(ClassMapInterface $pluggablesToPlugs)
    {
        $this->classMap = $pluggablesToPlugs;
    }

    public function clonedClassMap(): ClassMapInterface
    {
        return deep_copy($this->classMap);
    }

    public function getPlugsQueue(string $pluggableName): PlugsQueueInterface
    {
        $this->assertSetPlugsPath($pluggableName);
        $this->assertPlugsPath();
        $fileReturn = filePhpReturnForPath($this->plugsPath)
            ->withStrict(false);
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
