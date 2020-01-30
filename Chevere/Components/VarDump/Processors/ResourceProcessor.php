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

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\Type\Interfaces\TypeInterface;

final class ResourceProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::RESOURCE;
    }

    protected function process(): void
    {
        $resourceString = (string) $this->varFormat->dumpeable()->var();
        $this->val = $resourceString;
        $resource = get_resource_type($this->varFormat->dumpeable()->var());
        $this->info = 'type=' . $resource;
    }
}
