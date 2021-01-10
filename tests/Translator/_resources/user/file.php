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

$var = __('var');
$varF = __f('%s var', 'a');
$varT = __t('var %t', [
    '%t' => 'one',
]);
$nVar = __n('var', 'vars', 1);
$nVarF = __nf('%d var', '%d vars', 1, 123);
$nVarF = __nt('%v var', '%v vars', 1, [
    '%v' => 123,
]);
