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

namespace Chevere\Interfaces\Pluggable;

use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Pluggable\PluggableNotRegisteredException;
use Chevere\Exceptions\Pluggable\PlugsFileNotExistsException;
use Chevere\Exceptions\Pluggable\PlugsQueueInterfaceException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;

/**
 * Describes the component in charge of the interaction of pluggable and their plugs in the filesystem.
 */
interface PluginsInterface
{
    public function __construct(ClassMapInterface $pluggablesToPlugs);

    /**
     * Provides access to the a the cloned class map instance.
     */
    public function clonedClassMap(): ClassMapInterface;

    /**
     * Returns the plugs queue for the given `$pluggableName`.
     *
     * @throws PluggableNotRegisteredException
     * @throws PlugsFileNotExistsException
     * @throws RuntimeException
     * @throws PlugsQueueInterfaceException
     */
    public function getPlugsQueue(string $pluggableName): PlugsQueueInterface;
}
