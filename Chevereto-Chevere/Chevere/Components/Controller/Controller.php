<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controller;

use Chevere\Components\Hook\Traits\HookTrait;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Hooks\HookableContract;

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
abstract class Controller implements ControllerContract, HookableContract
{
    use HookTrait;

    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];

    /** @var AppContract */
    private $app;

    final public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    final public function app(): AppContract
    {
        return $this->app;
    }

    final public function content(): string
    {
        return $this->getContent() ?? '';
    }

    abstract public function __invoke(): void;

    /**
     * This method is used to retrieve the contents of the "document"
     * Child classes may implement this manually or using Controller Traits
     */
    abstract public function getContent(): string;
}
