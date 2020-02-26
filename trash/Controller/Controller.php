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

namespace Chevere\Components\Controller;

use Chevere\Components\Hooks\Traits\HookableTrait;
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Hooks\Interfaces\HookableInterface;

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
abstract class Controller implements ControllerInterface
{
    use HookableTrait;

    const TYPE_DECLARATIONS = ['array', 'callable', 'bool', 'float', 'int', 'string', 'iterable'];
    const OPTIONS = [];

    /** @var AppInterface */
    private $app;

    final public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    final public function app(): AppInterface
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
