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

return [
    'Chevere\Components\Hooks\Tests\MyHookable' => '%hooksPath%Chevere/Components/Hooks/Tests/MyHookable/hooks.php',
    'Chevere\Components\Hooks\Tests\MyHookableWithCorruptedHooks' => '%hooksPath%Chevere/Components/Hooks/Tests/MyHookableWithCorruptedHooks/hooks.php',
    'Chevere\Components\Hooks\Tests\MyHookableWithMissingHooks' => 'error.php'
];
