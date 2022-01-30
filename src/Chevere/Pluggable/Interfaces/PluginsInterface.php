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

namespace Chevere\Pluggable\Interfaces;

use Chevere\ClassMap\Interfaces\ClassMapInterface;
use Chevere\Pluggable\Exceptions\PluggableNotRegisteredException;
use Chevere\Pluggable\Exceptions\PlugsFileNotExistsException;
use Chevere\Throwable\Exceptions\RuntimeException;

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
     */
    public function getPlugsQueue(string $pluggableName): PlugsQueueInterface;
}
