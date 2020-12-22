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

use Chevere\Exceptions\Pluggable\PluggableAnchorNotExistsException;
use Chevere\Exceptions\Pluggable\PluggableAnchorsException;
use Chevere\Exceptions\Pluggable\PluggableNotExistsException;
use Chevere\Exceptions\Pluggable\PlugInterfaceException;

/**
 * Describes the component in charge of asserting a plug.
 */
interface AssertPlugInterface
{
    /**
     * @throws PlugInterfaceException
     * @throws PluggableNotExistsException
     * @throws PluggableAnchorsException
     * @throws PluggableAnchorNotExistsException
     */
    public function __construct(PlugInterface $plug);

    /**
     * Provides access to the plug type instance.
     */
    public function plugType(): PlugTypeInterface;

    /**
     * Provides access to the plug instance.
     */
    public function plug(): PlugInterface;
}
