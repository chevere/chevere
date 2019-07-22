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

namespace Chevere\App;

use RuntimeException;
use Chevere\Runtime\Runtime;
use Chevere\HttpFoundation\Request;

abstract class AppStatic
{
    /** @var App */
    protected static $instance;

    /** @var Runtime */
    protected static $defaultRuntime;

    protected static function setStaticInstance(App $app)
    {
        static::$instance = $app;
    }

    public static function setDefaultRuntime(Runtime $runtime): void
    {
        static::$defaultRuntime = $runtime;
    }

    public static function getDefaultRuntime(): Runtime
    {
        return static::$defaultRuntime;
    }

    /**
     * Provides access to the App Request instance.
     */
    public static function requestInstance(): ?Request
    {
        // Request isn't there when doing cli (unless you run the request command)
        if (isset(static::$instance) && isset(static::$instance->request)) {
            return static::$instance->request;
        }

        return null;
    }

    /**
     * Provides access to the App Runtime instance.
     */
    public static function runtimeInstance(): Runtime
    {
        if (isset(static::$instance) && $runtimeInstance = static::$instance->runtime) {
            return $runtimeInstance;
        }
        throw new RuntimeException('NO RUNTIME INSTANCE EVERYTHING BURNS!');
    }
}
