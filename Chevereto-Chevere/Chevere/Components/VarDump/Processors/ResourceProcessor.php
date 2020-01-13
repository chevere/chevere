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

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use function ChevereFn\stringReplaceFirst;

final class ResourceProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return VarDumpInterface::TYPE_RESOURCE;
    }

    protected function process(): void
    {
        $resourceString = (string) $this->varDump->var();
        $this->val = $resourceString;
        $resource = get_resource_type($this->varDump->var());
        $this->info = 'type=' . $resource;
    }
}
