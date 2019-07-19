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

namespace Chevereto\Chevere;

use Exception;
use LogicException;

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
 * Controller is the defacto controller in Chevereto\Chevere.
 */
abstract class Controller implements Interfaces\ControllerInterface
{
    use Traits\HookableTrait;

    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];

    /** @var App */
    private $app;

    /** @var string */
    private $filename;

    /** @var string Controller description */
    protected static $description;

    /** @var array Controller resources [propName => className] */
    protected static $resources;

    /** @var array Parameters passed via headers */
    protected static $parameters;

    public function getRoute(): ?Route
    {
        return $this->getApp()->route;
    }

    public function getApi(): ?Api
    {
        return $this->getApp()->api;
    }

    public function setResponse(Response $response): Interfaces\ControllerInterface
    {
        $this->getApp()->response = $response;

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->getApp()->response;
    }

    public function setApp(App $app): Interfaces\ControllerInterface
    {
        $this->app = $app;

        return $this;
    }

    public function getApp(): App
    {
        return $this->app;
    }

    public function invoke(string $controller, ...$parameters)
    {
        $that = $this->getCallable($controller);
        if (!is_callable($that)) {
            throw new Exception(
                (string) (new Message('Expected %s callable, %t provided.'))
                    ->code('%s', '$controller')
                    ->code('%t', gettype($controller))
            );
        }
        // Pass this to that so you can this while you that dawg!
        foreach (get_object_vars($this) as $k => $v) {
            $that->{$k} = $v;
        }

        return $that(...$parameters);
    }

    protected function getCallable(string $controller): callable
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

    protected function handleFilemane()
    {
        if (!File::exists($this->filename)) {
            throw new Exception(
                (string) (new Message("Unable to invoke controller %s (filename doesn't exists)."))
                ->code('%s', $this->filename)
            );
        }
    }

    public function __invoke()
    {
        // throw new LogicException(
        //     (string)
        //         (new Message('Class %c Must implement its own %s method.'))
        //             ->code()
        // );
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
