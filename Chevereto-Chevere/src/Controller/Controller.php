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

namespace Chevere\Controller;

use LogicException;
use Chevere\HttpFoundation\Response;
use Chevere\Message;
use Chevere\Hooking\Hook;
use Chevere\Traits\HookableTrait;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\Controller\ControllerContract;

// Define a hookable code entry:
// $this->hook('myHook', function ($that) use ($var) {
//     $that->bar = 'foo'; // $that is $this (the controller instance)
//     $var = 'foobar'; // Alters $var since it hass been passed by the 'use' constructor.
// });

// Hooks for 'myHook' should be defined using:
// Hook::bind('myHook@controller:file', Hook::BEFORE, function ($that) {
//     $that->source .= ' nosehaceeso no';
// });

/**
 * Controller is the defacto controller in Chevere.
 */
class Controller implements ControllerContract
{
    use HookableTrait;

    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];

    /** @var AppContract */
    protected $app;

    /** @var string */
    protected $filename;

    /** @var string Controller description */
    protected static $description;

    /** @var array Controller resources [propName => className] */
    protected static $resources;

    /** @var array Parameters passed via headers */
    protected static $parameters;

    /**
     * You must provide your own __invoke.
     */
    // public function __invoke()
    // {
    //     throw new LogicException(
    //         (new Message('Class %c must implement a %m method.'))
    //             ->code('%c', get_class($this))
    //             ->code('%m', 'public __invoke')
    //             ->toString()
    //     );
    // }

    final public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    final public function setResponse(Response $response): ControllerContract
    {
        $this->app->setResponse($response);

        return $this;
    }

    final public static function description(): string
    {
        return static::$description ?? '';
    }

    final public static function resources(): array
    {
        return static::$resources ?? [];
    }

    final public static function parameters(): array
    {
        return static::$parameters ?? [];
    }
}
