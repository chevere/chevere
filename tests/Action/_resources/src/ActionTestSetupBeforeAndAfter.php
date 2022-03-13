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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Action\Action;

final class ActionTestSetupBeforeAndAfter extends Action
{
    private int $before = 0;

    private int $after = 0;

    public function run(): array
    {
        return [];
    }

    public function before(): int
    {
        return $this->before;
    }
    
    public function after(): int
    {
        return $this->after;
    }

    protected function setUpBefore(): void
    {
        $this->before = 1;
    }

    protected function setUpAfter(): void
    {
        $this->after = $this->before + 1;
    }
}
