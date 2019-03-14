<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Exception;
use ReflectionClass;

// Define a hookable code entry:
// * $this is a Controller instance.
// $this->hook('myHook', function ($that) use ($var) {
//     $that->bar = 'foo'; // $that is $this (the controller instance)
//     $var = 'foobar'; // Alters $var since it hass been passed by the 'use' constructor.
// });

// Hooks for 'myHook' should be defined using:
// Hook::bind('myHook@controller:file', Hook::BEFORE, function ($that) {
//     $that->source .= ' nosehaceeso no';
// });

/**
 * Controller is the defacto controller in Chevereto\Core.
 *
 * @see Hookable
 * @see Interfaces\Controller
 * @see Interfaces\APIs
 */
class Controller extends Hookable implements Interfaces\ControllerInterface
{
    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];
    // const METHODS = ['POST'];

    // This allows to trace whom reads and write the non-accessible properties.
    // use Traits\PropSet;
    // use Traits\PropGet;

    protected $routing;

    /** @var App */
    protected $app;
    
    // TODO: Traits\App;

    public function hasApp() : bool
    {
        return $this->app instanceof App;
    }
    public function getApp() : App
    {
        return $this->app;
    }
    public function setApp(App $app) : self
    {
        $this->app = $app;
        return $this;
    }
    /**
     * Invoke another controller.
     *
     * @param string $controller Path handle. Start with @, to use the caller dir as root context.
     * @param mixed $parameters Invoke pararameter or parameters (array).
     *
     * @return mixed Output array or whatever the controller may output.
     */
    public function invoke(string $controller, $parameters = null)
    {
        if (gettype($parameters) != 'array') {
            $parameters = [$parameters];
        }
        if (class_exists($controller)) {
            // $r = new ReflectionClass($controller);
            // if ($r->hasMethod('__invoke') == false) {
            //     throw new ControllerException(
            //         (new Message("Missing %s method in class %c"))
            //         ->code('%s', '__invoke')
            //         ->code('%c', $controller)
            //     );
            // }
            $that = new $controller;
        } else {
            $controllerArgs = [$controller];
            if (Utils\Str::startsWith('@', $controller)) {
                $context = dirname(debug_backtrace(false, 1)[0]['file']);
                $controllerArgs = [substr($controller, 1), $context];
            }
            $filename = Path::fromHandle(...$controllerArgs);
            if (File::exists($filename) == false) {
                throw new Exception(
                    (new Message("Unable to invoke controller %s (filename doesn't exists)."))
                    ->code('%s', $filename)
                );
            }
            $that = Load::php($filename);
        }
        if (is_callable($that) == false) {
            throw new Exception(
                (new Message('Expected %s callable, %t provided.'))
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
    /**
     * Stores $_VARS for handling,
     */
    // protected function setProperties(array $properties) : void
    // {
    //     foreach ($properties as $k => $v) {
    //         $this->{$k} = $v;
    //     }
    // }
}
class ControllerException extends CoreException
{
}
