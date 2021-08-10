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

namespace Chevere\Components\Workflow\Attributes;

use Attribute;
use Chevere\Components\Attribute\Relation;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Workflow\WorkflowProviderInterface;

#[Attribute]
final class Provider extends Relation
{
    /**
     * @param string $attribute A workflow provider name.
     */
    public function __construct(protected string $attribute)
    {
        if (!is_subclass_of($attribute, WorkflowProviderInterface::class)) {
            throw new InvalidArgumentException(
                message: (new Message("The attribute `%attribute%` doesn't implement the %interface% interface."))
                    ->code('%attribute%', $attribute)
                    ->code('%interface%', WorkflowProviderInterface::class)
            );
        }
    }
}
