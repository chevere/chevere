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

namespace Chevereto\Chevere\Traits;

use Chevereto\Chevere\Hook;

/**
 * This class provides a hookable API allowing to define anchor points where
 * external code can be added on execution.
 *
 * For anchors defined in object context, the object itself will be passed
 * throught the hookable, making possible to execute external code that
 * will interact directly with the object.
 *
 * Your hookable entries can be defined to allow code injection before and or
 * after your hookable code.
 *
 * Any of your classes can provide Hookeable functionality by simply extending
 * this class.
 *
 * Pretend you have the following section inside your code:
 *
 *      $this->prop = 'value';
 *      $this->process($this->prop);
 *
 * That code cast process($this->prop). If you want to allow external code
 * there, simly wrap the above code in something like this:
 *
 *      $value = 'value';
 *      $this->hookable('anchor', function($that) use ($value) {
 *           $that->prop = $value;
 *      });
 *      $this->process($this->prop);
 *
 * In the above example, hooks can be registered before and after. Hooks before
 * will be executed before $that->prop = $value; $that is $this.
 *
 * A hook should be defined like this:
 *
 *      Hookable::after('anchor@relativePath:basename', function($that) {
 *           $that->prop = filter($that->prop);
 *      });
 *
 * The object is passed directly, so the methods and properties will be
 * accessible based on class visibility scope.
 *
 * @see Controller
 * @see SimpleController
 * @see Router
 */
trait HookableTrait
{
    /**
     * Register and run hookable code entries before and after.
     *
     * Hook::before('anchor@relativePath:basename', function($that) {
     *      $that
     * });
     *
     * @param string   $anchor   hook anchor
     * @param callable $callable callable
     */
    public function hookable(string $anchor, callable $callable): void
    {
        Hook::exec($anchor, $callable, $this);
    }

    /**
     * Register a hookable entry before.
     *
     * @see hookable()
     */
    public function hookableBefore(string $anchor, callable $callable): void
    {
        Hook::execBefore($anchor, $callable, $this);
    }

    /**
     * Register a hookable entry after.
     *
     * @see hookable()
     */
    public function hookableAfter(string $anchor, callable $callable): void
    {
        Hook::execAfter($anchor, $callable, $this);
    }

    /*
     * Static version of hookable()
     *
     * Static versions are limited as $this is not being passed through.
     * No variable can be touched, it just adds procedures.
     *
     * @see hookable()
     */
    // public static function section(string $anchor, callable $callable) : void
    // {
    //     Hook::exec(...func_get_args());
    // }
    /*
     * Static version of hookableBefore
     *
     * @see hookableBefore()
     */
    // public static function before(string $anchor, callable $callable) : void
    // {
    //     Hook::execBefore(...func_get_args());
    // }
    /*
     * Static version hookable after
     *
     * @see hookableAfter()
     */
    // public static function after(string $anchor, callable $callable) : void
    // {
    //     Hook::execAfter(...func_get_args());
    // }
}
