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
use Chevere\Components\Attribute\Dispatch as AttributeDispatch;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;

#[Attribute]
final class Dispatch extends AttributeDispatch
{
    public const QUEUE = 'queue';

    public const INSTANT = 'instant';
    
    /**
     * @param string $attribute A dispatch event name.
     * @throws InvalidArgumentException If $attribute doesn't implement WorkflowProviderInterface.
     */
    public function __construct(protected string $attribute)
    {
        if (!in_array($attribute, static::knownEvents())) {
            throw new InvalidArgumentException(
                message: (new Message("The attribute `%attribute%` is not of any of the known event types: %types%."))
                    ->code('%attribute%', $attribute)
                    ->code('%types%', implode(' ', static::knownEvents()))
            );
        }
    }

    public static function knownEvents(): array
    {
        return [self::QUEUE, self::INSTANT];
    }
}
