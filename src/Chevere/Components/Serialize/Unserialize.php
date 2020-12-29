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

namespace Chevere\Components\Serialize;

use Chevere\Components\Message\Message;
use function Chevere\Components\Type\debugType;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Serialize\UnserializeInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Exception;
use Throwable;

final class Unserialize implements UnserializeInterface
{
    private $var;

    private TypeInterface $type;

    public function __construct(string $unserializable)
    {
        try {
            $this->var = @unserialize($unserializable);
            if ($this->var === false) {
                throw new Exception('Passed string is not unserializable');
            }
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                new Message($e->getMessage()),
            );
        }

        $this->type = new Type(debugType($this->var));
    }

    public function var()
    {
        return $this->var;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }
}
