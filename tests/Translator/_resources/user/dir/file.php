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

$var = __('foo');
$varF = __f('%s foo', 'a');
$varT = __t('foo %t', [
    '%t' => 'one',
]);
$nVar = __n('foo', 'foos', 1);
$nVarF = __nf('%d foo', '%d foos', 1, 123);
$nVarF = __nt('%v foo', '%v foos', 1, [
    '%v' => 123,
]);
