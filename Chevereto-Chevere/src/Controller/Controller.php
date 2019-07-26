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
use Chevere\App\App;
use Chevere\Hooking\Hook;
use Chevere\Interfaces\ControllerInterface;
use Chevere\Traits\HookableTrait;

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
class Controller implements ControllerInterface
{
    use HookableTrait;

    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];

    /** @var App */
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
    public function __invoke()
    {
        throw new LogicException(
            (new Message('Class %c must implement a %m method.'))
                ->code('%c', get_class($this))
                ->code('%m', 'public __invoke')
                ->toString()
        );
    }

    public function setResponse(Response $response)
    {
        $this->app->response = $response;
    }

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function invoke(string $controller, ...$parameters)
    {
        $that = $this->getCallable($controller);
        if (!is_callable($that)) {
            throw new LogicException(
                (new Message('Expected %s callable, %t provided.'))
                    ->code('%s', '$controller')
                    ->code('%t', gettype($controller))
                    ->toString()
            );
        }
        // Pass this to that so you can this while you that dawg!
        foreach (get_object_vars($this) as $k => $v) {
            $that->{$k} = $v;
        }

        return $that(...$parameters);
    }

    private function getCallable(string $controller): callable
    {
        if (class_exists($controller)) {
            return new $controller();
        } else {
            throw new LogicException('NO CALLABLE');
            // $controllerArgs = [$controller];
            // if (Utility\Str::startsWith('@', $controller)) {
            //     $context = dirname(debug_backtrace(0, 1)[0]['file']);
            //     $controllerArgs = [substr($controller, 1), $context];
            // }
            // dd(debug_backtrace(0, 2), $context, $controllerArgs);
            // $this->filename = Path::fromHandle(...$controllerArgs);
            // $this->handleFilemane();
            // return Load::php($this->filename);
        }
    }

    private function handleFilemane()
    {
        if (!File::exists($this->filename)) {
            throw new LogicException(
                (new Message("Unable to invoke controller %s (filename doesn't exists)."))
                    ->code('%s', $this->filename)
                    ->toString()
            );
        }
    }

    final public static function getDescription(): ?string
    {
        return static::$description;
    }

    final public static function getResources(): ?array
    {
        return static::$resources;
    }

    final public static function getParameters(): ?array
    {
        return static::$parameters;
    }
}
