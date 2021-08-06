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

namespace Chevere\Components\Controller\Attributes;

use Attribute;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Workflow\WorkflowProviderInterface;

#[Attribute]
class RelationWorkflow extends Relation
{
    /**
     * @param string $relation A workflow provider name.
     * @throws InvalidArgumentException If $relation doesn't implement WorkflowProviderInterface.
     */
    public function __construct(protected string $relation)
    {
        if (!is_subclass_of($relation, WorkflowProviderInterface::class)) {
            throw new InvalidArgumentException(
                message: (new Message("The relation `%relation%` doesn't implement the %interface% interface."))
                    ->code('%relation%', $relation)
                    ->code('%interface%', WorkflowProviderInterface::class)
            );
        }
    }
}
